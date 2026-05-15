<?php

namespace App\Services;

use App\Models\Device;
use App\Models\JadwalListrik;
use App\Models\KontrolListrik;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

final class PeminjamanJadwalSyncService
{
    public function syncOnApprove(Peminjaman $peminjaman): JadwalListrik
    {
        $this->deleteJadwalForPeminjaman($peminjaman->id);

        $waktuMulai = $this->normalizeTime((string) $peminjaman->waktu_mulai);
        $waktuSelesai = $this->normalizeTime((string) $peminjaman->waktu_selesai);
        $tanggalMulai = Carbon::parse($peminjaman->tanggal_mulai)->startOfDay();
        $tanggalSelesai = Carbon::parse($peminjaman->tanggal_selesai)->startOfDay();

        $device = Device::query()
            ->where('ruangan_id', $peminjaman->ruangan_id)
            ->orderBy('id')
            ->first();

        $jadwal = JadwalListrik::create([
            'ruangan_id' => $peminjaman->ruangan_id,
            'peminjaman_id' => $peminjaman->id,
            'device_id' => $device?->id,
            'selected_days' => $this->selectedDaysBetween($tanggalMulai, $tanggalSelesai),
            'start_time' => $waktuMulai,
            'end_time' => $waktuSelesai,
            'automation_action' => 'on',
            'schedule_status' => 'active',
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'status_listrik' => 'nyala',
            'tanggal_mulai' => $tanggalMulai->toDateString(),
            'tanggal_selesai' => $tanggalSelesai->toDateString(),
        ]);

        $this->turnRoomDevicesOn($peminjaman->ruangan_id, $peminjaman->reviewed_by);

        return $jadwal;
    }

    public function deleteJadwalForPeminjaman(int $peminjamanId): void
    {
        JadwalListrik::query()
            ->where('peminjaman_id', $peminjamanId)
            ->delete();
    }

    /**
     * @return list<string>
     */
    private function selectedDaysBetween(Carbon $start, Carbon $end): array
    {
        $days = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $days[] = strtolower($d->englishDayOfWeek);
        }

        return array_values(array_unique($days));
    }

    private function turnRoomDevicesOn(string $ruanganId, ?int $userId): void
    {
        $devices = Device::query()->where('ruangan_id', $ruanganId)->orderBy('id')->get();

        foreach ($devices as $device) {
            try {
                Http::timeout(5)->get($this->deviceCommandUrl($device, 'on'));
            } catch (ConnectionException) {
                // IoT tidak terjangkau — jadwal tetap dibuat dengan status nyala
            }

            KontrolListrik::create([
                'user_id' => $userId,
                'ruangan_id' => $ruanganId,
                'device_id' => $device->id,
                'aksi' => 'on',
            ]);
        }
    }

    private function deviceCommandUrl(Device $device, string $action): string
    {
        $ip = trim((string) $device->ip_address);

        if (! str_starts_with($ip, 'http://') && ! str_starts_with($ip, 'https://')) {
            $ip = 'http://'.$ip;
        }

        return rtrim($ip, '/').'/'.ltrim($action, '/');
    }

    private function normalizeTime(string $time): string
    {
        $time = trim($time);

        return strlen($time) > 5 ? substr($time, 0, 5) : $time;
    }
}
