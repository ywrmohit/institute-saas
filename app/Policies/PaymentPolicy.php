<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || in_array($user->role, ['franchise_owner', 'branch_admin', 'accountant']);
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($user->franchise_id !== $payment->franchise_id) return false;
        if ($user->isBranchAdmin() && $user->branch_id !== null) {
            return $user->branch_id === $payment->branch_id;
        }
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || in_array($user->role, ['franchise_owner', 'branch_admin', 'accountant']);
    }

    public function update(User $user, Payment $payment): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->isFranchiseOwner() && $user->franchise_id === $payment->franchise_id;
    }

    public function delete(User $user, Payment $payment): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->isFranchiseOwner() && $user->franchise_id === $payment->franchise_id;
    }
}
