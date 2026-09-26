<?php

namespace App\Models\Scopes;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // If running in console (e.g. migrations, seeders) without tenant context, skip
        if (app()->runningInConsole() && !app()->has('current_tenant_id')) {
            return;
        }

        $tenantId = null;

        // 1. Check if Filament has an active tenant
        if (class_exists(Filament::class) && Filament::hasTenancy() && Filament::getTenant()) {
            $tenantId = Filament::getTenant()->id;
        }
        // 2. Check manually set tenant context (e.g. API / Jobs / Student portal)
        elseif (app()->has('current_tenant_id')) {
            $tenantId = app('current_tenant_id');
        }
        // 3. Fallback to authenticated user's franchise_id (if not super_admin in central panel)
        elseif (auth()->check()) {
            $user = auth()->user();
            if ($user->role !== 'super_admin' || $user->franchise_id !== null) {
                $tenantId = $user->franchise_id;
            }
        }

        if ($tenantId !== null) {
            $builder->where($model->getTable() . '.franchise_id', $tenantId);
        }
    }
}
