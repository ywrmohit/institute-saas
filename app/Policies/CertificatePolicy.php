<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || in_array($user->role, ['franchise_owner', 'branch_admin', 'trainer']);
    }

    public function view(User $user, Certificate $certificate): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->franchise_id === $certificate->franchise_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || in_array($user->role, ['franchise_owner', 'branch_admin']);
    }

    public function update(User $user, Certificate $certificate): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->isFranchiseOwner() && $user->franchise_id === $certificate->franchise_id;
    }

    public function delete(User $user, Certificate $certificate): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->isFranchiseOwner() && $user->franchise_id === $certificate->franchise_id;
    }
}
