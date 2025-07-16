<?php

namespace App\Observers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Ambil data asli (sebelum diubah)
        $original = $user->getOriginal();
        // Ambil data baru (setelah diubah)
        $changes = $user->getChanges();

        // Kita hanya tertarik pada perubahan gaji dan role
        $trackedAttributes = ['gaji_pokok', 'tarif_lembur_per_jam', 'role'];

        // Cek apakah ada perubahan pada kolom yang kita lacak
        $relevantChanges = array_intersect_key($changes, array_flip($trackedAttributes));

        if (count($relevantChanges) > 0) {
            AuditLog::create([
                'user_id' => Auth::id(), // Admin yang melakukan perubahan
                'activity' => 'memperbarui_data_user',
                'auditable_type' => User::class,
                'auditable_id' => $user->id, // User yang datanya diubah
                'old_values' => json_encode(array_intersect_key($original, $relevantChanges)),
                'new_values' => json_encode($relevantChanges),
            ]);
        }
    }
}