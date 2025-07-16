<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Karyawan\OvertimeController as KaryawanOvertimeController;

// Halaman landing page utama
Route::get('/', function () {
    return view('welcome');
});

// Route fallback dashboard bawaan Breeze, bisa kita hapus nanti
Route::get('/dashboard', function () {
    // Logika redirect cerdas berdasarkan role
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    if (auth()->user()->isAtasan()) {
        return redirect()->route('atasan.dashboard');
    }
    if (auth()->user()->isKaryawan()) {
        return redirect()->route('karyawan.dashboard');
    }
    // Fallback jika tidak ada role
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
        
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/qrcode', [QrCodeController::class, 'show'])->name('qrcode.show');
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
            // Ambil data absensi hari ini untuk ditampilkan di dashboard
            $todayAttendance = \App\Models\Attendance::where('user_id', auth()->id())
                ->where('attendance_date', now()->toDateString())
                ->first();
                
            return view('karyawan.dashboard', compact('todayAttendance'));
        })->name('dashboard');
        
        // ROUTE UNTUK PROSES ABSENSI
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        
        // ROUTE UNTUK LEMBUR KARYAWAN
        Route::get('overtime', [KaryawanOvertimeController::class, 'index'])->name('overtime.index');
        Route::get('overtime/create', [KaryawanOvertimeController::class, 'create'])->name('overtime.create');
        Route::post('overtime', [KaryawanOvertimeController::class, 'store'])->name('overtime.store');
    });
});


// Memanggil route otentikasi bawaan Breeze
require __DIR__.'/auth.php';
