<?php

namespace App\Observers;

use App\Models\Leave;
use App\Observers\Concerns\RecordsAuditLog;

class LeaveObserver
{
    use RecordsAuditLog;

    /**
     * Handle the Leave "created" event.
     */
    public function created(Leave $leave): void
    {
        $this->recordAudit($leave, 'mengajukan_cuti', null, $leave->getAttributes());
    }

    /**
     * Handle the Leave "updated" event.
     */
    public function updated(Leave $leave): void
    {
        $changes = $leave->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $activity = match ($changes['status'] ?? null) {
            'approved' => 'menyetujui_cuti',
            'rejected' => 'menolak_cuti',
            default => 'memperbarui_cuti',
        };

        $original = array_intersect_key($leave->getOriginal(), $changes);

        $this->recordAudit($leave, $activity, $original, $changes);
    }

    /**
     * Handle the Leave "deleted" event.
     */
    public function deleted(Leave $leave): void
    {
        $this->recordAudit($leave, 'menghapus_cuti', $leave->getAttributes(), null);
    }
}
