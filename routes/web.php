<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Karyawan\OvertimeController as KaryawanOvertimeController;
use App\Http\Controllers\Atasan\OvertimeController as AtasanOvertimeController;
use App\Http\Controllers\Karyawan\LeaveController as KaryawanLeaveController;
use App\Http\Controllers\Atasan\LeaveController as AtasanLeaveController;
use App\Http\Controllers\Laporan\AbsensiController as LaporanAbsensiController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PayrollController as AdminPayrollController;
use App\Http\Controllers\Admin\AuditLogController; 

// Halaman landing page utama
Route::get('/', function () {
    return view('welcome');
});

// Route fallback dashboard bawaan Breeze
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    if (auth()->user()->isAtasan()) {
        return redirect()->route('atasan.dashboard');
    }
    if (auth()->user()->isKaryawan()) {
        return redirect()->route('karyawan.dashboard');
    }
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

        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');

        Route::get('payroll', [AdminPayrollController::class, 'index'])->name('payroll.index');
        Route::get('payroll/{user}/payslip', [AdminPayrollController::class, 'generatePayslip'])->name('payroll.payslip');
         // ROUTE BARU UNTUK AUDIT LOG
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // GROUPING ROUTE UNTUK ROLE ATASAN
    Route::middleware('role:atasan')->prefix('atasan')->name('atasan.')->group(function () {
        Route::get('/dashboard', function () {
            return view('atasan.dashboard');
        })->name('dashboard');
        
        Route::get('overtime', [AtasanOvertimeController::class, 'index'])->name('overtime.index');
        Route::get('overtime/{overtime}/edit', [AtasanOvertimeController::class, 'edit'])->name('overtime.edit');
        Route::put('overtime/{overtime}', [AtasanOvertimeController::class, 'update'])->name('overtime.update');

        Route::get('leaves', [AtasanLeaveController::class, 'index'])->name('leaves.index');
        Route::get('leaves/{leave}/edit', [AtasanLeaveController::class, 'edit'])->name('leaves.edit');
        Route::put('leaves/{leave}', [AtasanLeaveController::class, 'update'])->name('leaves.update');
    });

    // GROUPING ROUTE UNTUK ROLE KARYAWAN
    Route::middleware('role:karyawan')->prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/dashboard', function () {
            $todayAttendance = \App\Models\Attendance::where('user_id', auth()->id())
                ->where('attendance_date', now()->toDateString())
                ->first();
            return view('karyawan.dashboard', compact('todayAttendance'));
        })->name('dashboard');
        
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        
        Route::get('overtime', [KaryawanOvertimeController::class, 'index'])->name('overtime.index');
        Route::get('overtime/create', [KaryawanOvertimeController::class, 'create'])->name('overtime.create');
        Route::post('overtime', [KaryawanOvertimeController::class, 'store'])->name('overtime.store');

        Route::get('leaves', [KaryawanLeaveController::class, 'index'])->name('leaves.index');
        Route::get('leaves/create', [KaryawanLeaveController::class, 'create'])->name('leaves.create');
        Route::post('leaves', [KaryawanLeaveController::class, 'store'])->name('leaves.store');
    });

    // GROUPING ROUTE UNTUK LAPORAN
    Route::middleware(['role:admin,atasan'])->prefix('laporan')->name('laporan.')->group(function() {
        Route::get('absensi', [LaporanAbsensiController::class, 'index'])->name('absensi.index');
    });
});


// Memanggil route otentikasi bawaan Breeze
require __DIR__.'/auth.php';
      