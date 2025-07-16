<?php

namespace App\Observers;

use App\Models\Overtime;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class OvertimeObserver
{
    /**
     * Handle the Overtime "created" event.
     */
    public function created(Overtime $overtime): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'activity' => 'mengajukan_lembur',
            'auditable_type' => Overtime::class,
            'auditable_id' => $overtime->id,
            'new_values' => json_encode($overtime->toArray()),
        ]);
    }

    /**
     * Handle the Overtime "updated" event.
     */
    public function updated(Overtime $overtime): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'activity' => 'memproses_lembur',
            'auditable_type' => Overtime::class,
            'auditable_id' => $overtime->id,
            'old_values' => json_encode($overtime->getOriginal()),
            'new_values' => json_encode($overtime->getChanges()),
        ]);
    }
}
