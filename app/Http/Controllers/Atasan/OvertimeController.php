<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Overtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OvertimeController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Overtime::class);

        $overtimes = Overtime::with('user')->orderBy('created_at', 'desc')->paginate(10);

        return view('atasan.overtime.index', compact('overtimes'));
    }

    public function edit(Overtime $overtime)
    {
        $this->authorize('view', $overtime);

        return view('atasan.overtime.edit', compact('overtime'));
    }

    public function update(Request $request, Overtime $overtime)
    {
        $this->authorize('update', $overtime);

        if ($overtime->status !== 'pending') {
            return redirect()->route('atasan.overtime.index')->with('error', 'Pengajuan lembur ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'approver_notes' => 'nullable|string|max:255',
        ]);

        $overtime->update([
            'status' => $request->status,
            'approver_notes' => $request->approver_notes,
            'approved_by' => Auth::id(),
            'is_read' => false, // Tandai belum dibaca karyawan, memicu notifikasi
        ]);

        return redirect()->route('atasan.overtime.index')->with('status', 'Pengajuan lembur berhasil diproses.');
    }
}