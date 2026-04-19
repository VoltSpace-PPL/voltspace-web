<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitoringEnergi extends Model
{
    use HasFactory;

    protected $table = 'monitoring_energis';

    protected $fillable = [
        'ruangan_id',
        'bulan',
        'tahun',
        'konsumsi_kwh',
        'waktu_record',
    ];

    protected $casts = [
        'waktu_record' => 'datetime',
        'konsumsi_kwh' => 'decimal:2',
    ];

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }
}
