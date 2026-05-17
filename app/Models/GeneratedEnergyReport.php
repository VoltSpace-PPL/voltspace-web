<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedEnergyReport extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'jenis_periode',
        'bulan',
        'tahun',
        'total_kwh_ringkasan',
        'meta',
        'disk',
        'path',
        'mime',
        'size_bytes',
    ];

    protected $casts = [
        'meta' => 'array',
        'total_kwh_ringkasan' => 'decimal:3',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
