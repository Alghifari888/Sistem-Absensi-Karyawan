<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Overtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    /**
     * Menampilkan riwayat pengajuan lembur karyawan.
     */
    public function index()
    {
        // Ambil data lembur hanya untuk user yang sedang login, urutkan dari yang terbaru
        $overtimes = Overtime::where('user_id', Auth::id())
                            ->orderBy('overtime_date', 'desc')
                            ->paginate(10);

        return view('karyawan.overtime.index', compact('overtimes'));
    }

    /**
     * Menampilkan form untuk membuat pengajuan lembur baru.
     */
    public function create()
    {
        return view('karyawan.overtime.create');
    }

    /**
     * Menyimpan pengajuan lembur baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'overtime_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:255',
        ]);

        // Buat pengajuan lembur baru
        Overtime::create([
            'user_id' => Auth::id(),
            'overtime_date' => $request->overtime_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
            'status' => 'pending', // Status awal selalu pending
        ]);

        return redirect()->route('karyawan.overtime.index')->with('status', 'Pengajuan lembur berhasil dibuat.');
    }
}