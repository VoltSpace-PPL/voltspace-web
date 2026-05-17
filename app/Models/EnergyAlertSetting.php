<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnergyAlertSetting extends Model
{
    protected $fillable = [
        'high_usage_threshold_kwh',
        'peak_demand_limit_kw',
        'updated_by',
    ];

    protected $casts = [
        'high_usage_threshold_kwh' => 'decimal:3',
        'peak_demand_limit_kw' => 'decimal:3',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function current(): self
    {
        $row = self::query()->orderBy('id')->first();
        if ($row) {
            return $row;
        }

        return self::query()->create([
            'high_usage_threshold_kwh' => 100,
            'peak_demand_limit_kw' => 10,
        ]);
    }
}
