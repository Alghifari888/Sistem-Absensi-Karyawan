<?php

namespace App\Observers;

use App\Models\Leave;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class LeaveObserver
{
    /**
     * Handle the Leave "created" event.
     */
    public function created(Leave $leave): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'activity' => 'mengajukan_cuti',
            'auditable_type' => Leave::class,
            'auditable_id' => $leave->id,
            'new_values' => json_encode($leave->toArray()),
        ]);
    }

    /**
     * Handle the Leave "updated" event.
     */
    public function updated(Leave $leave): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'activity' => 'memproses_cuti',
            'auditable_type' => Leave::class,
            'auditable_id' => $leave->id,
            'old_values' => json_encode($leave->getOriginal()),
            'new_values' => json_encode($leave->getChanges()),
        ]);
    }
}
