<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;

    /* PK is a string (e.g. "R-101") – must disable auto-increment */
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nama_ruangan',
        'lokasi',
        'kapasitas',
        'status',
    ];

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function jadwalListrik(): HasMany
    {
        return $this->hasMany(JadwalListrik::class);
    }

    public function kontrolListrik(): HasMany
    {
        return $this->hasMany(KontrolListrik::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function monitoringEnergi(): HasMany
    {
        return $this->hasMany(MonitoringEnergi::class);
    }

    public function laporanEnergi(): HasMany
    {
        return $this->hasMany(LaporanEnergi::class);
    }
}
