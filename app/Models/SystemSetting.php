<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'franchise_id',
        'key',
        'value',
    ];

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    public static function get(string $key, ?int $franchiseId = null, $default = null)
    {
        $cacheKey = "sys_setting_{$franchiseId}_{$key}";

        return Cache::remember($cacheKey, 86400, function () use ($key, $franchiseId, $default) {
            $setting = self::where('key', $key)
                ->where('franchise_id', $franchiseId)
                ->first();

            return ($setting && $setting->value !== null && $setting->value !== '') ? $setting->value : $default;
        });
    }

    public static function set(string $key, $value, ?int $franchiseId = null): void
    {
        self::updateOrCreate(
            ['franchise_id' => $franchiseId, 'key' => $key],
            ['value' => $value]
        );

        Cache::forget("sys_setting_{$franchiseId}_{$key}");
    }

    protected static function booted(): void
    {
        static::saved(function ($model) {
            Cache::forget("sys_setting_{$model->franchise_id}_{$model->key}");
        });

        static::deleted(function ($model) {
            Cache::forget("sys_setting_{$model->franchise_id}_{$model->key}");
        });
    }
}
