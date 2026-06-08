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
