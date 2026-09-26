<?php

namespace App\Policies;

use App\Models\Franchise;
use App\Models\User;

class FranchisePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isFranchiseOwner();
    }

    public function view(User $user, Franchise $franchise): bool
    {
        return $user->isSuperAdmin() || ($user->franchise_id === $franchise->id);
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Franchise $franchise): bool
    {
        return $user->isSuperAdmin() || ($user->isFranchiseOwner() && $user->franchise_id === $franchise->id);
    }

    public function delete(User $user, Franchise $franchise): bool
    {
        return $user->isSuperAdmin();
    }
}
