<?php

namespace App\Policies;

use App\Models\Overtime;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OvertimePolicy
{
    /**
     * Hanya Atasan yang bisa melihat semua pengajuan lembur.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAtasan();
    }

    /**
     * Karyawan bisa melihat pengajuan lemburnya sendiri, atau Atasan bisa melihat.
     */
    public function view(User $user, Overtime $overtime): bool
    {
        return $user->id === $overtime->user_id || $user->isAtasan();
    }

    /**
     * Semua karyawan bisa membuat pengajuan lembur.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Hanya atasan yang bisa meng-update (approve/reject) pengajuan.
     */
    public function update(User $user, Overtime $overtime): bool
    {
        return $user->isAtasan();
    }

    /**
     * Tidak ada yang boleh menghapus data lembur (sebagai contoh).
     */
    public function delete(User $user, Overtime $overtime): bool
    {
        return false;
    }
}