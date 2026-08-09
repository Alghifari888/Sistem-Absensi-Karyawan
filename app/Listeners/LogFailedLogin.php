<?php

namespace App\Listeners;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    /**
     * Handle the event.
     *
     * Hanya dicatat saat email cocok dengan akun yang ada (password salah),
     * karena kolom auditable_id tidak boleh null sehingga percobaan dengan
     * email yang tidak terdaftar (Failed::$user null) tidak bisa diarahkan
     * ke model manapun.
     */
    public function handle(Failed $event): void
    {
        if (! $event->user) {
            return;
        }

        AuditLog::create([
            'user_id' => null,
            'activity' => 'login_gagal',
            'auditable_type' => User::class,
            'auditable_id' => $event->user->id,
        ]);
    }
}
