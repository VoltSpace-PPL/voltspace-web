<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'ip_address',
        'ruangan_id',
    ];

    protected $appends = ['device_code'];

    public function getDeviceCodeAttribute(): string
    {
        return 'DV-' . str_pad((string)$this->id, 3, '0', STR_PAD_LEFT);
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function kontrolListriks(): HasMany
    {
        return $this->hasMany(KontrolListrik::class);
    }

    public function jadwalListriks(): HasMany
    {
        return $this->hasMany(JadwalListrik::class);
    }
}

