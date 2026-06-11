<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\JadwalListrik;
use App\Models\Device;
use App\Models\KontrolListrik;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $now = Carbon::now();
    $today = $now->toDateString();
    $currentTime = $now->format('H:i');
    $currentDay = strtolower($now->englishDayOfWeek);

    $schedules = JadwalListrik::query()
        ->where('schedule_status', 'active')
        ->get();

    foreach ($schedules as $schedule) {
        $shouldRunOff = false;

        if ($schedule->tanggal_selesai && $schedule->tanggal_selesai === $today) {
            if (substr($schedule->end_time, 0, 5) === $currentTime) {
                $shouldRunOff = true;
            }
        } elseif (!$schedule->tanggal_selesai && is_array($schedule->selected_days) && in_array($currentDay, $schedule->selected_days)) {
            if (substr($schedule->end_time, 0, 5) === $currentTime) {
                $shouldRunOff = true;
            }
        }

        if ($shouldRunOff && $schedule->ruangan_id) {
            $devices = Device::query()->where('ruangan_id', $schedule->ruangan_id)->get();
            foreach ($devices as $device) {
                $ip = trim((string) $device->ip_address);
                if ($ip) {
                    if (!str_starts_with($ip, 'http://') && !str_starts_with($ip, 'https://')) {
                        $ip = 'http://' . $ip;
                    }
                    $url = rtrim($ip, '/') . '/off';
                    try {
                        Http::timeout(5)->get($url);
                    } catch (ConnectionException $e) {
                        // ignore
                    }
                }
                KontrolListrik::create([
                    'user_id' => null,
                    'ruangan_id' => $schedule->ruangan_id,
                    'device_id' => $device->id,
                    'aksi' => 'off',
                ]);
            }
            
            // Mark the schedule as inactive if it's tied to a one-time booking
            if ($schedule->peminjaman_id) {
                $schedule->update(['schedule_status' => 'inactive', 'status_listrik' => 'mati']);
            } else {
                $schedule->update(['status_listrik' => 'mati']);
            }
        }
    }
})->everyMinute();

/**
 * Job 1: Kirim pengingat kepada admin untuk pengajuan pending yang belum diproses
 * setelah 1×24 jam (dan belum melebihi 2×24 jam).
 * Penanda: catatan_admin diset ke '__reminder_sent__' agar tidak dikirim berulang.
 */
Schedule::call(function () {
    $now        = Carbon::now();
    $threshold1 = $now->copy()->subHours(24); // lebih dari 24 jam lalu
    $threshold2 = $now->copy()->subHours(48); // belum 48 jam

    $pendings = \App\Models\Peminjaman::query()
        ->where('status', 'pending')
        ->where('created_at', '<=', $threshold1)
        ->where('created_at', '>', $threshold2)
        ->where(function ($q) {
            // hanya yang belum pernah dikirim reminder (ditandai di catatan_admin)
            $q->whereNull('catatan_admin')
              ->orWhere('catatan_admin', '!=', '__reminder_sent__');
        })
        ->get();

    foreach ($pendings as $p) {
        // Tandai sudah dikirim reminder agar tidak berulang
        $p->update(['catatan_admin' => '__reminder_sent__']);

        // Log untuk debugging / bisa diperluas ke email/push di masa depan
        \Illuminate\Support\Facades\Log::info('[AutoReminder] Pengajuan peminjaman #'.$p->id.' belum diproses selama >24 jam. Admin perlu segera memproses.');
    }

    if ($pendings->count() > 0) {
        \Illuminate\Support\Facades\Log::info('[AutoReminder] '.$pendings->count().' pengajuan menunggu keputusan admin.');
    }
})->hourly();

/**
 * Job 2: Auto-reject pengajuan pending yang sudah melewati 2×24 jam tanpa keputusan admin.
 */
Schedule::call(function () {
    $threshold = Carbon::now()->subHours(48);

    $expired = \App\Models\Peminjaman::query()
        ->where('status', 'pending')
        ->where('created_at', '<=', $threshold)
        ->get();

    foreach ($expired as $p) {
        $p->update([
            'status'        => 'ditolak',
            'catatan_admin' => 'Pengajuan otomatis ditolak oleh sistem karena tidak ada keputusan admin dalam 2×24 jam.',
            'reviewed_at'   => Carbon::now(),
        ]);

        \Illuminate\Support\Facades\Log::info('[AutoReject] Pengajuan peminjaman #'.$p->id.' ditolak otomatis (melewati batas 2×24 jam).');
    }

    if ($expired->count() > 0) {
        \Illuminate\Support\Facades\Log::info('[AutoReject] '.$expired->count().' pengajuan ditolak otomatis.');
    }
})->hourly();
