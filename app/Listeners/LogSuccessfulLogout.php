<?php

namespace App\Listeners;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        // Logout via session expiry / guard cleanup bisa terjadi tanpa user (mis. token invalid).
        if (! $event->user) {
            return;
        }

        AuditLog::create([
            'user_id' => $event->user->id,
            'activity' => 'logout',
            'auditable_type' => User::class,
            'auditable_id' => $event->user->id,
        ]);
    }
}
