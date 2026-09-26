<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;

class BranchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || in_array($user->role, ['franchise_owner', 'branch_admin', 'trainer', 'accountant']);
    }

    public function view(User $user, Branch $branch): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($user->franchise_id !== $branch->franchise_id) return false;
        if ($user->branch_id !== null && $user->role === 'branch_admin') {
            return $user->branch_id === $branch->id;
        }
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isFranchiseOwner();
    }

    public function update(User $user, Branch $branch): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($user->franchise_id !== $branch->franchise_id) return false;
        if ($user->isFranchiseOwner()) return true;
        if ($user->isBranchAdmin()) return $user->branch_id === $branch->id;
        return false;
    }

    public function delete(User $user, Branch $branch): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->isFranchiseOwner() && $user->franchise_id === $branch->franchise_id;
    }
}
