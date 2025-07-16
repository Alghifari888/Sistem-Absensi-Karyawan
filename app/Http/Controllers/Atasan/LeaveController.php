<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LeaveController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Leave::class);

        $leaves = Leave::with('user')->orderBy('start_date', 'desc')->paginate(10);

        return view('atasan.leaves.index', compact('leaves'));
    }

    public function edit(Leave $leave)
    {
        $this->authorize('view', $leave);

        return view('atasan.leaves.edit', compact('leave'));
    }

    public function update(Request $request, Leave $leave)
    {
        $this->authorize('update', $leave);

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'approver_notes' => 'nullable|string|max:255',
        ]);

        $leave->update([
            'status' => $request->status,
            'approver_notes' => $request->approver_notes,
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('atasan.leaves.index')->with('status', 'Pengajuan berhasil diproses.');
    }
}