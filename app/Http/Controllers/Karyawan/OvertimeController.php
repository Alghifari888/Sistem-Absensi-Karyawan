<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Overtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    public function index()
    {
        $overtimes = Overtime::where('user_id', Auth::id())
                            ->orderBy('overtime_date', 'desc')
                            ->paginate(10);

        // Tandai semua pengajuan milik user ini sebagai sudah dibaca (menghilangkan badge notifikasi)
        Overtime::where('user_id', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true]);

        return view('karyawan.overtime.index', compact('overtimes'));
    }

    public function create()
    {
        return view('karyawan.overtime.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'overtime_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:255',
        ]);

        Overtime::create([
            'user_id' => Auth::id(),
            'overtime_date' => $request->overtime_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('karyawan.overtime.index')->with('status', 'Pengajuan lembur berhasil dibuat.');
    }
}