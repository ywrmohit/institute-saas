<?php

namespace App\Traits;

use App\Models\Franchise;
use App\Models\Scopes\TenantScope;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (empty($model->franchise_id)) {
                if (class_exists(Filament::class) && Filament::hasTenancy() && Filament::getTenant()) {
                    $model->franchise_id = Filament::getTenant()->id;
                } elseif (app()->has('current_tenant_id')) {
                    $model->franchise_id = app('current_tenant_id');
                } elseif (auth()->check() && auth()->user()->franchise_id) {
                    $model->franchise_id = auth()->user()->franchise_id;
                }
            }
        });
    }

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }
}
