<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || in_array($user->role, ['franchise_owner', 'branch_admin', 'trainer', 'accountant']);
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($user->franchise_id !== $student->franchise_id) return false;
        if ($user->isBranchAdmin() && $user->branch_id !== null) {
            return $user->branch_id === $student->branch_id;
        }
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || in_array($user->role, ['franchise_owner', 'branch_admin', 'accountant']);
    }

    public function update(User $user, Student $student): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($user->franchise_id !== $student->franchise_id) return false;
        if ($user->isBranchAdmin() && $user->branch_id !== null) {
            return $user->branch_id === $student->branch_id;
        }
        return in_array($user->role, ['franchise_owner', 'branch_admin']);
    }

    public function delete(User $user, Student $student): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->isFranchiseOwner() && $user->franchise_id === $student->franchise_id;
    }
}
