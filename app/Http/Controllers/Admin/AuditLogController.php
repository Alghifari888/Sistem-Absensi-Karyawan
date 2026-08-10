<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Menampilkan halaman daftar audit log, dengan filter pencarian.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        // Filter: rentang waktu
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter: user pelaku
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter: jenis aktivitas
        if ($request->filled('activity')) {
            $query->where('activity', $request->activity);
        }

        // Filter: kata kunci di detail perubahan (old_values / new_values)
        if ($request->filled('detail')) {
            $keyword = $request->detail;
            $query->where(function ($q) use ($keyword) {
                $q->where('old_values', 'like', "%{$keyword}%")
                  ->orWhere('new_values', 'like', "%{$keyword}%");
            });
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        // Data untuk dropdown filter
        $users = User::whereIn('id', AuditLog::whereNotNull('user_id')->distinct()->pluck('user_id'))
                      ->orderBy('name')
                      ->get(['id', 'name']);

        $activities = AuditLog::select('activity')
                                ->distinct()
                                ->orderBy('activity')
                                ->pluck('activity');

        return view('admin.audit-logs.index', compact('logs', 'users', 'activities'));
    }
}