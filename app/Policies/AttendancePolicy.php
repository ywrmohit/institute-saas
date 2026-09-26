<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || in_array($user->role, ['franchise_owner', 'branch_admin', 'trainer', 'accountant']);
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($user->franchise_id !== $attendance->franchise_id) return false;
        if ($user->isTrainer()) {
            return $attendance->batch?->trainer_id === $user->id;
        }
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || in_array($user->role, ['franchise_owner', 'branch_admin', 'trainer']);
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($user->franchise_id !== $attendance->franchise_id) return false;
        if ($user->isTrainer()) {
            return $attendance->batch?->trainer_id === $user->id;
        }
        return in_array($user->role, ['franchise_owner', 'branch_admin']);
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->isFranchiseOwner() && $user->franchise_id === $attendance->franchise_id;
    }
}
