<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        $setting = self::where('key', $key)
            ->where('franchise_id', $franchiseId)
            ->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value, ?int $franchiseId = null): void
    {
        self::updateOrCreate(
            ['franchise_id' => $franchiseId, 'key' => $key],
            ['value' => $value]
        );
    }
}
