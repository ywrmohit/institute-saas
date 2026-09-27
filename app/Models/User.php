<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'franchise_id',
        'branch_id',
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'avatar',
        'designation',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (User $user) {
            try {
                if ($user->role && ! $user->hasRole($user->role)) {
                    $user->syncRoles([$user->role]);
                }
            } catch (\Throwable $e) {
                // If roles are not yet seeded or exist in DB, gracefully ignore
            }
        });
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isFranchiseOwner(): bool
    {
        return $this->role === 'franchise_owner';
    }

    public function isBranchAdmin(): bool
    {
        return $this->role === 'branch_admin';
    }

    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }

    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function trainedBatches(): HasMany
    {
        return $this->hasMany(Batch::class, 'trainer_id');
    }

    public function receivedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'received_by_user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // Filament User Authorization
    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($panel->getId() === 'admin') {
            // Central Admin Panel is for Super Admin
            return $this->isSuperAdmin();
        }

        if ($panel->getId() === 'franchise') {
            if ($this->isStudent()) {
                return false;
            }
            return !empty($this->franchise_id) || $this->isSuperAdmin();
        }

        return false;
    }

    // Filament Multi-Tenancy Contracts
    public function getTenants(Panel $panel): array|Collection
    {
        if ($this->isSuperAdmin()) {
            return Franchise::where('status', 'active')->get();
        }

        if ($this->franchise_id) {
            return Franchise::where('id', $this->franchise_id)->get();
        }

        return collect();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->franchise_id === $tenant->id && $this->status === 'active';
    }
}
