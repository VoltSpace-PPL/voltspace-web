<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;

    /* PK is a string (e.g. "RM-001") – must disable auto-increment */
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'kode',
        'nama_ruangan',
        'kapasitas',
        'lantai',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ruangan $model): void {
            if (! $model->id) {
                $max = 0;
                foreach (Ruangan::query()->where('id', 'like', 'RM-%')->pluck('id') as $rid) {
                    $n = (int) substr((string) $rid, 3);
                    $max = max($max, $n);
                }
                $model->id = 'RM-'.str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
            }
            $model->kode ??= $model->id;
        });
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'ruangan_id', 'id');
    }

    public function jadwalListrik(): HasMany
    {
        return $this->hasMany(JadwalListrik::class, 'ruangan_id', 'id');
    }

    public function jadwalPemakaian(): HasMany
    {
        return $this->hasMany(JadwalPemakaianRuangan::class, 'ruangan_id', 'id');
    }

    public function kontrolListrik(): HasMany
    {
        return $this->hasMany(KontrolListrik::class, 'ruangan_id', 'id');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'ruangan_id', 'id');
    }

    public function monitoringEnergi(): HasMany
    {
        return $this->hasMany(MonitoringEnergi::class, 'ruangan_id', 'id');
    }

}
