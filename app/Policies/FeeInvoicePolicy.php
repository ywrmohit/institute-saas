<?php

namespace App\Policies;

use App\Models\FeeInvoice;
use App\Models\User;

class FeeInvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || in_array($user->role, ['franchise_owner', 'branch_admin', 'accountant']);
    }

    public function view(User $user, FeeInvoice $invoice): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($user->franchise_id !== $invoice->franchise_id) return false;
        if ($user->isBranchAdmin() && $user->branch_id !== null) {
            return $user->branch_id === $invoice->branch_id;
        }
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || in_array($user->role, ['franchise_owner', 'branch_admin', 'accountant']);
    }

    public function update(User $user, FeeInvoice $invoice): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($user->franchise_id !== $invoice->franchise_id) return false;
        return in_array($user->role, ['franchise_owner', 'branch_admin', 'accountant']);
    }

    public function delete(User $user, FeeInvoice $invoice): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->isFranchiseOwner() && $user->franchise_id === $invoice->franchise_id;
    }
}
