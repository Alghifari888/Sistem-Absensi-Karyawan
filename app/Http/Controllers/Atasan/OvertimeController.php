<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Overtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Pastikan baris ini ada

class OvertimeController extends Controller
{
    use AuthorizesRequests; // Dan pastikan baris ini juga ada

    /**
     * Menampilkan daftar semua pengajuan lembur.
     */
    public function index()
    {
        // Otorisasi menggunakan Policy: hanya atasan yang bisa melihat.
        $this->authorize('viewAny', Overtime::class);

        // Ambil semua data lembur, urutkan dari yang terbaru
        $overtimes = Overtime::with('user')->orderBy('created_at', 'desc')->paginate(10);

        return view('atasan.overtime.index', compact('overtimes'));
    }

    /**
     * Menampilkan form untuk memproses (approve/reject) pengajuan.
     */
    public function edit(Overtime $overtime)
    {
        // Otorisasi menggunakan Policy: hanya atasan yang bisa melihat detail.
        $this->authorize('view', $overtime);

        return view('atasan.overtime.edit', compact('overtime'));
    }

    /**
     * Mengupdate status pengajuan lembur.
     */
    public function update(Request $request, Overtime $overtime)
    {
        // Otorisasi menggunakan Policy: hanya atasan yang bisa mengupdate.
        $this->authorize('update', $overtime);

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'approver_notes' => 'nullable|string|max:255',
        ]);

        $overtime->update([
            'status' => $request->status,
            'approver_notes' => $request->approver_notes,
            'approved_by' => Auth::id(), // Catat siapa yang memproses
        ]);

        return redirect()->route('atasan.overtime.index')->with('status', 'Pengajuan lembur berhasil diproses.');
    }
}
