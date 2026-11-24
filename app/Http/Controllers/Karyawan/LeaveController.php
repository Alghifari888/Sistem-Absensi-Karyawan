<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::where('user_id', Auth::id())
                        ->orderBy('start_date', 'desc')
                        ->paginate(10);

        // Tandai semua pengajuan milik user ini sebagai sudah dibaca (menghilangkan badge notifikasi)
        Leave::where('user_id', Auth::id())
             ->where('is_read', false)
             ->update(['is_read' => true]);

        return view('karyawan.leaves.index', compact('leaves'));
    }

    public function create()
    {
        $sisaKuota = Auth::user()->sisaKuotaCuti();

        return view('karyawan.leaves.create', compact('sisaKuota'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:cuti,izin,sakit',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
            'proof_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

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
            $filePath = $request->file('proof_document')->store('proofs', 'local');
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

    public function showProof(Leave $leave)
    {
        Gate::authorize('view', $leave);

        abort_unless($leave->proof_document && Storage::disk('local')->exists($leave->proof_document), 404);

        return Storage::disk('local')->response($leave->proof_document);
    }
}