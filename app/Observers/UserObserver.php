<?php

namespace App\Observers;

use App\Models\User;
use App\Observers\Concerns\RecordsAuditLog;

class UserObserver
{
    use RecordsAuditLog;

    // Password & remember_token tidak boleh masuk ke audit log.
    protected function auditExcept(): array
    {
        return ['password', 'remember_token'];
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->recordAudit($user, 'membuat_user', null, $user->getAttributes());
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $changes = $user->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $original = array_intersect_key($user->getOriginal(), $changes);

        $this->recordAudit($user, 'memperbarui_user', $original, $changes);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->recordAudit($user, 'menghapus_user', $user->getAttributes(), null);
    }
}
