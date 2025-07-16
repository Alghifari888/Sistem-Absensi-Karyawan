<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Menampilkan halaman daftar audit log.
     */
    public function index()
    {
        // Ambil semua log, urutkan dari yang paling baru, dengan paginasi
        $logs = AuditLog::with('user') // Eager load data user untuk efisiensi
                        ->latest()      // Urutkan berdasarkan created_at (terbaru dulu)
                        ->paginate(20);  // Tampilkan 20 log per halaman

        return view('admin.audit-logs.index', compact('logs'));
    }
}