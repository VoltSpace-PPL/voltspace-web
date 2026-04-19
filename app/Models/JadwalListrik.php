<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalListrik extends Model
{
    use HasFactory;

    protected $table = 'jadwal_listriks';

    protected $fillable = [
        'ruangan_id',
        'device_id',
        'selected_days',
        'start_time',
        'end_time',
        'automation_action',
        'schedule_status',
        'waktu_mulai',
        'waktu_selesai',
        'status_listrik',
    ];

    protected $casts = [
        'selected_days' => 'array',
    ];

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
