<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KelasAnggotaController;
use App\Http\Controllers\KelasKontenController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\TugasPengumpulanController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ForumKomentarController;
use App\Http\Controllers\SiswaKelasController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================================================
// PUBLIC ROUTES (Guest Only)
// ============================================================================

Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// ============================================================================
// PROTECTED ROUTES (Authenticated Users: Guru & Siswa)
// ============================================================================

Route::middleware('auth.custom')->group(function () {

    // ------------------------------------------------------------------------
    // DASHBOARD
    // ------------------------------------------------------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ------------------------------------------------------------------------
    // KELAS ROUTES
    // ------------------------------------------------------------------------

    // Kelas CRUD
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{kela}', [KelasController::class, 'show'])->name('kelas.show');
    Route::get('/kelas/{kela}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{kela}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    // Kelas Arsip (Guru Only)
    Route::get('/kelas-arsip', [KelasController::class, 'arsip'])->name('kelas.arsip');
    Route::put('/kelas/{id}/archive', [KelasController::class, 'archive'])->name('kelas.archive');
    Route::put('/kelas/{id}/restore', [KelasController::class, 'restore'])->name('kelas.restore');

    // ------------------------------------------------------------------------
    // KELAS KONTEN ROUTES (Guru Only)
    // ------------------------------------------------------------------------
    Route::post('/kelas/{id}/konten', [KelasKontenController::class, 'store'])->name('konten.store');
    Route::delete('/konten/{id}', [KelasKontenController::class, 'destroy'])->name('konten.destroy');
    Route::put('/konten/{id}', [KelasKontenController::class, 'update'])->name('konten.update');

    // ------------------------------------------------------------------------
    // KELAS ANGGOTA ROUTES (Guru Only)
    // ------------------------------------------------------------------------
    Route::resource('kelas-anggotas', KelasAnggotaController::class)->except(['show']);

    // ------------------------------------------------------------------------
    // SISWA JOIN/LEAVE KELAS (Siswa Only)
    // ------------------------------------------------------------------------
    Route::post('/siswa/join-kelas', [SiswaKelasController::class, 'joinKelas'])->name('siswa.join-kelas');
    Route::delete('/siswa/leave-kelas/{kelasId}', [SiswaKelasController::class, 'leaveKelas'])->name('siswa.leave-kelas');

    // ------------------------------------------------------------------------
    // TUGAS ROUTES
    // ------------------------------------------------------------------------

    // Tugas CRUD (Guru)
    Route::get('/kelas/{kelas}/tugas', [TugasController::class, 'index'])->name('tugas.index');
    Route::post('/kelas/{kelas}/tugas', [TugasController::class, 'store'])->name('tugas.store');
    Route::get('/tugas/{id}', [TugasController::class, 'show'])->name('tugas.show');
    Route::put('/kelas/{kelas}/tugas/{id}', [TugasController::class, 'update'])->name('tugas.update');
    Route::delete('/kelas/{kelas}/tugas/{id}', [TugasController::class, 'destroy'])->name('tugas.destroy');

    // Tugas Pengumpulan (Siswa)
    Route::post('/tugas/{tugas_id}/submit', [TugasPengumpulanController::class, 'store'])->name('tugas.submit');

    // Tugas Penilaian (Guru)
    Route::put('/tugaspengumpulan/{id}', [TugasPengumpulanController::class, 'update'])->name('tugaspengumpulan.update');
    Route::delete('/tugaspengumpulan/{id}', [TugasPengumpulanController::class, 'destroy'])->name('tugaspengumpulan.destroy');

    // ------------------------------------------------------------------------
    // FORUM ROUTES
    // ------------------------------------------------------------------------

    // Forum List & Detail
    Route::get('/kelas/{id}/forum', [ForumController::class, 'index'])->name('kelas.forum');
    Route::get('/forum/{forum}', [ForumController::class, 'show'])->name('forum.show');

    // Forum Create & Store
    Route::get('/kelas/{kelas_id}/forum/create', [ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum/store', [ForumController::class, 'store'])->name('forum.store');

    // Forum Update & Delete
    Route::put('/forum/{forum}', [ForumController::class, 'update'])->name('forum.update');
    Route::delete('/forum/{forum}', [ForumController::class, 'destroy'])->name('forum.destroy');

    // ------------------------------------------------------------------------
    // FORUM KOMENTAR ROUTES
    // ------------------------------------------------------------------------
    Route::post('/forum-komentar', [ForumKomentarController::class, 'store'])->name('forum-komentar.store');
    Route::put('/forum-komentar/{id}', [ForumKomentarController::class, 'update'])->name('forum-komentar.update');
    Route::delete('/forum-komentar/{id}', [ForumKomentarController::class, 'destroy'])->name('forum-komentar.destroy');
});
