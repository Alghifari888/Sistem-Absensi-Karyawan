<?php

use Illuminate\Support\Facades\Route;

// General Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;

// Admin Controllers
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\PayrollController as AdminPayrollController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\SettingController;

// Atasan Controllers
use App\Http\Controllers\Atasan\LeaveController as AtasanLeaveController;
use App\Http\Controllers\Atasan\OvertimeController as AtasanOvertimeController;

// Karyawan Controllers
use App\Http\Controllers\Karyawan\LeaveController as KaryawanLeaveController;
use App\Http\Controllers\Karyawan\OvertimeController as KaryawanOvertimeController;

// Laporan Controller
use App\Http\Controllers\Laporan\AbsensiController as LaporanAbsensiController;

// Models for Dashboard Data
use App\Models\User;
use App\Models\Leave;
use App\Models\Overtime;
use App\Models\AuditLog;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Halaman landing page utama
Route::get('/', function () {
    return view('welcome');
});

// Route dashboard utama yang akan mengarahkan user berdasarkan role
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->isAtasan()) {
        return redirect()->route('atasan.dashboard');
    }
    if ($user->isKaryawan()) {
        return redirect()->route('karyawan.dashboard');
    }
    // Fallback jika tidak ada role
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// GROUPING ROUTE UNTUK SEMUA USER YANG SUDAH LOGIN
Route::middleware('auth')->group(function () {
    // Route untuk Profile (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ======================================================
    // GROUPING ROUTE UNTUK ROLE ADMIN
    // ======================================================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            $totalKaryawan = User::where('role', 'karyawan')->count();
            $totalAtasan = User::where('role', 'atasan')->count();
            $pendingLeaves = Leave::where('status', 'pending')->count();
            $pendingOvertimes = Overtime::where('status', 'pending')->count();
            $recentLogs = AuditLog::with('user')->latest()->take(5)->get();

            return view('admin.dashboard', compact('totalKaryawan', 'totalAtasan', 'pendingLeaves', 'pendingOvertimes', 'recentLogs'));
        })->name('dashboard');
        
        // Manajemen User
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        
        // Penggajian
        Route::get('payroll', [AdminPayrollController::class, 'index'])->name('payroll.index');
        Route::get('payroll/{user}/payslip', [AdminPayrollController::class, 'generatePayslip'])->name('payroll.payslip');
        
        // Pengaturan & Sistem
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/qrcode', [QrCodeController::class, 'show'])->name('qrcode.show');
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // ======================================================
    // GROUPING ROUTE UNTUK ROLE ATASAN
    // ======================================================
    Route::middleware('role:atasan')->prefix('atasan')->name('atasan.')->group(function () {
        Route::get('/dashboard', function () {
            $pendingLeaves = Leave::where('status', 'pending')->count();
            $pendingOvertimes = Overtime::where('status', 'pending')->count();
            $recentLeaves = Leave::with('user')->where('status', 'pending')->latest()->take(5)->get();
            $recentOvertimes = Overtime::with('user')->where('status', 'pending')->latest()->take(5)->get();

            return view('atasan.dashboard', compact('pendingLeaves', 'pendingOvertimes', 'recentLeaves', 'recentOvertimes'));
        })->name('dashboard');
        
        // Persetujuan Lembur
        Route::get('overtime', [AtasanOvertimeController::class, 'index'])->name('overtime.index');
        Route::get('overtime/{overtime}/edit', [AtasanOvertimeController::class, 'edit'])->name('overtime.edit');
        Route::put('overtime/{overtime}', [AtasanOvertimeController::class, 'update'])->name('overtime.update');

        // Persetujuan Cuti/Izin
        Route::get('leaves', [AtasanLeaveController::class, 'index'])->name('leaves.index');
        Route::get('leaves/{leave}/edit', [AtasanLeaveController::class, 'edit'])->name('leaves.edit');
        Route::put('leaves/{leave}', [AtasanLeaveController::class, 'update'])->name('leaves.update');
    });

    // ======================================================
    // GROUPING ROUTE UNTUK ROLE KARYAWAN
    // ======================================================
    Route::middleware('role:karyawan')->prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/dashboard', function () {
            $todayAttendance = \App\Models\Attendance::where('user_id', auth()->id())
                ->where('attendance_date', now()->toDateString())
                ->first();
            return view('karyawan.dashboard', compact('todayAttendance'));
        })->name('dashboard');
        
        // Absensi
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        
        // Pengajuan Lembur
        Route::get('overtime', [KaryawanOvertimeController::class, 'index'])->name('overtime.index');
        Route::get('overtime/create', [KaryawanOvertimeController::class, 'create'])->name('overtime.create');
        Route::post('overtime', [KaryawanOvertimeController::class, 'store'])->name('overtime.store');

        // Pengajuan Cuti/Izin
        Route::get('leaves', [KaryawanLeaveController::class, 'index'])->name('leaves.index');
        Route::get('leaves/create', [KaryawanLeaveController::class, 'create'])->name('leaves.create');
        Route::post('leaves', [KaryawanLeaveController::class, 'store'])->name('leaves.store');
    });

    // ======================================================
    // GROUPING ROUTE UNTUK LAPORAN (Admin & Atasan)
    // ======================================================
    Route::middleware(['role:admin,atasan'])->prefix('laporan')->name('laporan.')->group(function() {
        Route::get('absensi', [LaporanAbsensiController::class, 'index'])->name('absensi.index');
    });
});


// Memanggil route otentikasi bawaan Breeze
require __DIR__.'/auth.php';
