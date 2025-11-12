<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KelasController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth.custom')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard', [
            'user' => session('user_name'),
            'role' => session('role'),
        ]);
    })->name('dashboard');

// Kelas & komponennya
Route::resource('kelas', KelasController::class);
Route::resource('kelas-anggotas', KelasAnggotaController::class);
Route::resource('kelas-kontens', KelasKontenController::class);

});
