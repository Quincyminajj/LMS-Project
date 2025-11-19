<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KelasAnggotaController;
use App\Http\Controllers\KelasKontenController; 
use App\Http\Controllers\TugasController; 
use App\Http\Controllers\TugasPengumpulanController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ForumKomentarController;
use App\Models\Kelas;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth.custom')->group(function () {

    Route::get('/dashboard', function () {
        $kelas = Kelas::where('guru_nip', session('identifier'))
            ->where('status', '!=', 'Arsip')
            ->latest()->get();

        $kelasArsip = Kelas::where('guru_nip', session('identifier'))
                  ->where('status', 'Arsip')
                  ->count();
        $kelasAktif = Kelas::where('guru_nip', session('identifier'))
                  ->where(function($q){
                $q->whereNull('status')
                  ->orWhere('status', 'Aktif'); })->count();          

        return view('dashboard', [
            'user' => session('user_name'),
            'role' => session('role'),
            'kelas' => $kelas,
            'kelasAktif' => $kelasAktif,
            'kelasArsip' => $kelasArsip
        ]);
    })->name('dashboard');

    Route::get('/kelas/arsip', function () {
        $kelasArsip = Kelas::where('guru_nip', session('identifier'))
            ->where('status', 'Arsip')
            ->latest()
            ->get();  

        return view('kelas.arsip', compact('kelasArsip'));
    })->name('kelas.arsip');

    // Kelas CRUD
    Route::resource('kelas', KelasController::class);
    Route::resource('kelas-anggotas', KelasAnggotaController::class);
    Route::post('/kelas/{id}/konten', [KelasKontenController::class, 'store'])->name('konten.store');
    Route::post('/kelas/{id}/archive', [KelasController::class, 'archive'])->name('kelas.archive');
    Route::post('/kelas/{id}/restore', [KelasController::class, 'restore'])->name('kelas.restore');
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('kelas.destroy');


    // Tugas CRUD
    Route::resource('tugaspengumpulan', TugasPengumpulanController::class);
    Route::get('/kelas/{kelas}/tugas', [TugasController::class, 'index'])->name('tugas.index');
    Route::get('/tugas/{id}', [TugasController::class, 'showView'])->name('tugas.show');

    Route::post('/kelas/{kelas}/tugas', [TugasController::class, 'store'])->name('tugas.store');
    Route::put('/kelas/{kelas}/tugas/{id}', [TugasController::class, 'update'])->name('tugas.update');
    Route::delete('/kelas/{kelas}/tugas/{id}', [TugasController::class, 'destroy'])->name('tugas.destroy');

    // Forum
    Route::get('/kelas/{id}/forum', [ForumController::class, 'index'])->name('kelas.forum');
    Route::get('/kelas/{kelas_id}/forum/create', [ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum/store', [ForumController::class, 'store'])->name('forum.store');

    Route::get('/forum/{forum}', [ForumController::class, 'show'])->name('forum.show');
    Route::post('/forum/{forum}/comment', [ForumController::class, 'comment'])->name('forum.comment');

});
