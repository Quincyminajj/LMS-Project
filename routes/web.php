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
        $kelas = Kelas::where('guru_nip', session('identifier'))->latest()->get(); 

        return view('dashboard', [
            'user' => session('user_name'),
            'role' => session('role'),
            'kelas' => $kelas,
        ]);
    })->name('dashboard');

    // Kelas CRUD
    Route::resource('kelas', KelasController::class);
    Route::resource('kelas-anggotas', KelasAnggotaController::class);
    Route::post('/kelas/{id}/konten', [KelasKontenController::class, 'store'])->name('konten.store');

    // Tugas CRUD
    Route::apiResource('tugas', TugasController::class);
    Route::apiResource('pengumpulan', TugasPengumpulanController::class);

    // Forum dan Komentar
    Route::apiResource('forums', ForumController::class);
    Route::apiResource('komentars', ForumKomentarController::class);
});
