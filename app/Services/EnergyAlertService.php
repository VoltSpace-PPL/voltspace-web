<?php

namespace App\Services;

use App\Models\EnergyAlertSetting;
use App\Models\MonitoringEnergi;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class EnergyAlertService
{
    private const WARNING_RATIO = 0.85;

    /**
     * Alert dihitung real-time dari monitoring + ambang global (tidak disimpan ke DB).
     *
     * @return array{
     *     periode: array{tahun: int, bulan: int},
     *     threshold_kwh: float,
     *     alerts: list<array<string, mixed>>
     * }
     */
    public function buildAlerts(?int $year = null, ?int $month = null): array
    {
        $now = Carbon::now();
        $year = $year ?? (int) $now->year;
        $month = $month ?? (int) $now->month;

        $global = EnergyAlertSetting::current();
        $threshold = (float) $global->high_usage_threshold_kwh;
        $warningAt = $threshold * self::WARNING_RATIO;

        $perRoom = MonitoringEnergi::query()
            ->select('ruangan_id', DB::raw('SUM(konsumsi_kwh) as total_kwh'))
            ->where('tahun', $year)
            ->where('bulan', $month)
            ->groupBy('ruangan_id')
            ->pluck('total_kwh', 'ruangan_id');

        $rooms = Ruangan::query()->orderBy('id')->get(['id', 'kode', 'nama_ruangan']);
        $alerts = [];

        foreach ($rooms as $room) {
            $kwh = round((float) ($perRoom[$room->id] ?? 0), 3);
            $nama = $room->nama_ruangan;

            if ($kwh >= $threshold) {
                $alerts[] = [
                    'status' => 'exceeded',
                    'severity' => 'danger',
                    'ruangan_id' => $room->id,
                    'nama_ruangan' => $nama,
                    'total_kwh' => $kwh,
                    'threshold_kwh' => $threshold,
                    'persentase_ambang' => $threshold > 0 ? round(($kwh / $threshold) * 100, 1) : 100,
                    'message' => "{$nama} telah melebihi batas konsumsi ({$kwh} kWh / batas {$threshold} kWh).",
                ];

                continue;
            }

            if ($kwh >= $warningAt) {
                $sisa = round(max(0, $threshold - $kwh), 3);
                $alerts[] = [
                    'status' => 'almost_exceeded',
                    'severity' => 'warning',
                    'ruangan_id' => $room->id,
                    'nama_ruangan' => $nama,
                    'total_kwh' => $kwh,
                    'threshold_kwh' => $threshold,
                    'sisa_kwh_sebelum_batas' => $sisa,
                    'persentase_ambang' => $threshold > 0 ? round(($kwh / $threshold) * 100, 1) : 0,
                    'message' => "{$nama} hampir melebihi batas (tersisa {$sisa} kWh dari {$threshold} kWh).",
                ];
            }
        }

        return [
            'periode' => ['tahun' => $year, 'bulan' => $month],
            'threshold_kwh' => $threshold,
            'alerts' => $alerts,
        ];
    }
}
