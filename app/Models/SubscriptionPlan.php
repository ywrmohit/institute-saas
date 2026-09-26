<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'billing_interval',
        'max_branches',
        'max_students',
        'features',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_branches' => 'integer',
        'max_students' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function franchises(): HasMany
    {
        return $this->hasMany(Franchise::class);
    }
}
