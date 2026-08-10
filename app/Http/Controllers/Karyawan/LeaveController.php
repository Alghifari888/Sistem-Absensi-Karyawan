<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Menampilkan riwayat pengajuan cuti/izin.
     */
    public function index()
    {
        $leaves = Leave::where('user_id', Auth::id())
                        ->orderBy('start_date', 'desc')
                        ->paginate(10);

        return view('karyawan.leaves.index', compact('leaves'));
    }

    /**
     * Menampilkan form pengajuan baru.
     */
    public function create()
    {
        $sisaKuota = Auth::user()->sisaKuotaCuti();

        return view('karyawan.leaves.create', compact('sisaKuota'));
    }

    /**
     * Menyimpan pengajuan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:cuti,izin,sakit',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
            'proof_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Validasi kuota HANYA untuk tipe 'cuti' (cuti tahunan)
        if ($request->type === 'cuti') {
            $totalDays = Carbon::parse($request->start_date)
                ->diffInDays(Carbon::parse($request->end_date)) + 1;

            $sisaKuota = Auth::user()->sisaKuotaCuti();

            if ($totalDays > $sisaKuota) {
                return back()->withInput()->withErrors([
                    'start_date' => "Pengajuan cuti ({$totalDays} hari) melebihi sisa kuota Anda ({$sisaKuota} hari). Silakan ajukan sebagai 'Izin' jika tidak menggunakan jatah cuti tahunan.",
                ]);
            }
        }

        $filePath = null;
        if ($request->hasFile('proof_document')) {
            $filePath = $request->file('proof_document')->store('proofs', 'public');
        }

        Leave::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'proof_document' => $filePath,
            'status' => 'pending',
        ]);

        return redirect()->route('karyawan.leaves.index')->with('status', 'Pengajuan berhasil dibuat.');
    }
}