<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController; // <-- Tambahkan ini

// Halaman landing page utama
Route::get('/', function () {
    return view('welcome');
});

// Route fallback dashboard bawaan Breeze, bisa kita hapus nanti
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// GROUPING ROUTE UNTUK USER YANG SUDAH LOGIN
Route::middleware('auth')->group(function () {
    // Route untuk Profile (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // GROUPING ROUTE UNTUK ROLE ADMIN
Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // ROUTE BARU UNTUK PENGATURAN
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

    // GROUPING ROUTE UNTUK ROLE ATASAN
    Route::middleware('role:atasan')->prefix('atasan')->name('atasan.')->group(function () {
        Route::get('/dashboard', function () {
            return view('atasan.dashboard');
        })->name('dashboard');
        // Nanti route-route atasan lainnya ditaruh di sini
    });

    // GROUPING ROUTE UNTUK ROLE KARYAWAN
    Route::middleware('role:karyawan')->prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/dashboard', function () {
            return view('karyawan.dashboard');
        })->name('dashboard');
        // Nanti route-route karyawan lainnya ditaruh di sini
    });
});


// Memanggil route otentikasi bawaan Breeze
require __DIR__.'/auth.php';