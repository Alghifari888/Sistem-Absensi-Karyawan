<?php

namespace App\Observers;

use App\Models\Setting;
use App\Observers\Concerns\RecordsAuditLog;

class SettingObserver
{
    use RecordsAuditLog;

    /**
     * Handle the Setting "created" event.
     */
    public function created(Setting $setting): void
    {
        $this->recordAudit($setting, 'membuat_pengaturan', null, $setting->getAttributes());
    }

    /**
     * Handle the Setting "updated" event.
     */
    public function updated(Setting $setting): void
    {
        $changes = $setting->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $original = array_intersect_key($setting->getOriginal(), $changes);

        $this->recordAudit($setting, 'memperbarui_pengaturan', $original, $changes);
    }
}
