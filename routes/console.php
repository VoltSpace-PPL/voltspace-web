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

/**
 * Job: Electricity Schedule → kontrol relay ESP32.
 *
 * Aturan:
 * - AUTO ON  aktif pada rentang start_time sampai end_time.
 * - AUTO OFF aktif pada rentang start_time sampai end_time.
 * - Jika AUTO ON dan AUTO OFF bentrok, AUTO OFF menang.
 * - Contoh:
 *   08:00-17:00 AUTO ON, 11:00-12:00 AUTO OFF
 *   hasil: 08:00 ON, 11:00 OFF, 12:00 ON lagi, 17:00 OFF.
 */
Schedule::call(function () {
    $now = Carbon::now('Asia/Jakarta');
    $today = $now->toDateString();
    $currentTime = $now->format('H:i');
    $currentDay = strtolower($now->englishDayOfWeek);

    $normalizeTime = function ($time): string {
        return substr((string) $time, 0, 5);
    };

    $isTimeInRange = function (string $time, string $start, string $end): bool {
        $time = substr($time, 0, 5);
        $start = substr($start, 0, 5);
        $end = substr($end, 0, 5);

        // Normal: 08:00 - 17:00
        if ($start <= $end) {
            return $time >= $start && $time < $end;
        }

        // Lewat tengah malam: 22:00 - 05:00
        return $time >= $start || $time < $end;
    };

    $buildDeviceUrl = function (Device $device, string $endpoint): ?string {
        $ip = trim((string) $device->ip_address);

        if ($ip === '') {
            return null;
        }

        if (!str_starts_with($ip, 'http://') && !str_starts_with($ip, 'https://')) {
            $ip = 'http://' . $ip;
        }

        return rtrim($ip, '/') . '/' . ltrim($endpoint, '/');
    };

    $sendRelayCommand = function (Device $device, string $aksi, string $ruanganId) use ($buildDeviceUrl) {
        $commandUrl = $buildDeviceUrl($device, $aksi);

        if (!$commandUrl) {
            return;
        }

        $shouldSend = true;

        // Cek status agar tidak spam /on atau /off setiap menit.
        try {
            $statusUrl = $buildDeviceUrl($device, 'status');
            $statusRes = Http::timeout(3)->get($statusUrl);

            if ($statusRes->successful()) {
                $currentRelay = strtoupper((string) ($statusRes->json('relay') ?? ''));

                if ($aksi === 'on' && $currentRelay === 'ON') {
                    $shouldSend = false;
                }

                if ($aksi === 'off' && $currentRelay === 'OFF') {
                    $shouldSend = false;
                }
            }
        } catch (\Exception $e) {
            // Kalau status gagal dibaca, tetap coba kirim command.
        }

        if (!$shouldSend) {
            return;
        }

        try {
            Http::timeout(5)->get($commandUrl);

            // Dibungkus try-catch supaya scheduler tidak mati jika kolom user_id tidak nullable.
            try {
                KontrolListrik::create([
                    'user_id'    => null,
                    'ruangan_id' => $ruanganId,
                    'device_id'  => $device->id,
                    'aksi'       => $aksi,
                ]);
            } catch (\Exception $e) {
                // Log kontrol gagal disimpan, command IoT tetap sudah dikirim.
            }
        } catch (ConnectionException $e) {
            // IoT unreachable.
        }
    };

    $schedules = JadwalListrik::query()
        ->where('schedule_status', 'active')
        ->get()
        ->filter(function ($schedule) use ($today, $currentDay) {
            $tanggalMulai = $schedule->tanggal_mulai
                ? Carbon::parse($schedule->tanggal_mulai)->toDateString()
                : null;

            $tanggalSelesai = $schedule->tanggal_selesai
                ? Carbon::parse($schedule->tanggal_selesai)->toDateString()
                : null;

            // Date range guard.
            if ($tanggalMulai && $tanggalMulai > $today) {
                return false;
            }

            if ($tanggalSelesai && $tanggalSelesai < $today) {
                return false;
            }

            // Day of week guard.
            $selectedDays = $schedule->selected_days;

            if (is_string($selectedDays)) {
                $selectedDays = json_decode($selectedDays, true);
            }

            if (!is_array($selectedDays)) {
                $selectedDays = [];
            }

            if (!empty($selectedDays) && !in_array($currentDay, $selectedDays, true)) {
                return false;
            }

            return true;
        });

    $schedulesByRoom = $schedules->groupBy('ruangan_id');

    foreach ($schedulesByRoom as $ruanganId => $roomSchedules) {
        $activeNow = $roomSchedules->filter(function ($schedule) use ($currentTime, $normalizeTime, $isTimeInRange) {
            $startTime = $normalizeTime($schedule->start_time ?? $schedule->waktu_mulai);
            $endTime = $normalizeTime($schedule->end_time ?? $schedule->waktu_selesai);

            if (!$startTime || !$endTime) {
                return false;
            }

            return $isTimeInRange($currentTime, $startTime, $endTime);
        });

        $desiredAction = null;

        if ($activeNow->isNotEmpty()) {
            // Kalau ada AUTO OFF aktif, OFF menang dari AUTO ON.
            $hasOffSchedule = $activeNow->contains(function ($schedule) {
                return $schedule->automation_action === 'off';
            });

            $desiredAction = $hasOffSchedule ? 'off' : 'on';
        } else {
            // Kalau tepat di jam selesai salah satu schedule, matikan relay.
            $endedNow = $roomSchedules->contains(function ($schedule) use ($currentTime, $normalizeTime) {
                $endTime = $normalizeTime($schedule->end_time ?? $schedule->waktu_selesai);
                return $endTime === $currentTime;
            });

            if ($endedNow) {
                $desiredAction = 'off';
            }
        }

        if (!$desiredAction) {
            continue;
        }

        $devices = Device::query()
            ->where('ruangan_id', $ruanganId)
            ->get();

        foreach ($devices as $device) {
            $sendRelayCommand($device, $desiredAction, (string) $ruanganId);
        }

        JadwalListrik::query()
            ->whereIn('id', $roomSchedules->pluck('id')->values()->all())
            ->update([
                'status_listrik' => $desiredAction === 'on' ? 'nyala' : 'mati',
            ]);
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