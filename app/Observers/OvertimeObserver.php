<?php

namespace App\Observers;

use App\Models\Overtime;
use App\Observers\Concerns\RecordsAuditLog;

class OvertimeObserver
{
    use RecordsAuditLog;

    /**
     * Handle the Overtime "created" event.
     */
    public function created(Overtime $overtime): void
    {
        $this->recordAudit($overtime, 'mengajukan_lembur', null, $overtime->getAttributes());
    }

    /**
     * Handle the Overtime "updated" event.
     */
    public function updated(Overtime $overtime): void
    {
        $changes = $overtime->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $activity = match ($changes['status'] ?? null) {
            'approved' => 'menyetujui_lembur',
            'rejected' => 'menolak_lembur',
            default => 'memperbarui_lembur',
        };

        $original = array_intersect_key($overtime->getOriginal(), $changes);

        $this->recordAudit($overtime, $activity, $original, $changes);
    }

    /**
     * Handle the Overtime "deleted" event.
     */
    public function deleted(Overtime $overtime): void
    {
        $this->recordAudit($overtime, 'menghapus_lembur', $overtime->getAttributes(), null);
    }
}
