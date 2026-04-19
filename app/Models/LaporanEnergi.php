<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanEnergi extends Model
{
    use HasFactory;

    protected $table = 'laporan_energis';

    protected $fillable = [
        'ruangan_id',
        'bulan',
        'tahun',
        'total_kwh',
    ];

    protected $casts = [
        'total_kwh' => 'decimal:2',
    ];

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }
}
