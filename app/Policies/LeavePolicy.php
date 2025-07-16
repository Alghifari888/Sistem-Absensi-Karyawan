<?php

namespace App\Policies;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeavePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAtasan();
    }

    public function view(User $user, Leave $leave): bool
    {
        return $user->id === $leave->user_id || $user->isAtasan();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Leave $leave): bool
    {
        return $user->isAtasan();
    }
    
    public function delete(User $user, Leave $leave): bool
    {
        return false;
    }
}