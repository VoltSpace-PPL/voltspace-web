<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LaporanEnergi;
use App\Models\MonitoringEnergi;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        [$year, $month, $selectedDate] = $this->resolvePeriod($request);
        $summary = $this->buildSummary($selectedDate->copy()->subMonth());
        $trend = $this->buildTrend($year);
        $rooms = $this->buildRoomsPayload($selectedDate);

        return response()->json([
            'period' => [
                'year' => $year,
                'month' => $month,
                'month_name' => $selectedDate->translatedFormat('F'),
                'label' => $selectedDate->format('Y-m'),
            ],
            'summary' => $summary,
            'trend' => $trend,
            'rooms' => $rooms,
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        [$year, $month, $selectedDate] = $this->resolvePeriod($request);
        $summary = $this->buildSummary($selectedDate->copy()->subMonth());

        return response()->json([
            'period' => [
                'year' => $year,
                'month' => $month,
                'month_name' => $selectedDate->translatedFormat('F'),
                'label' => $selectedDate->format('Y-m'),
            ],
            'summary' => $summary,
        ]);
    }

    public function trend(Request $request): JsonResponse
    {
        $year = (int) $request->integer('year', (int) Carbon::now()->year);

        return response()->json([
            'trend' => $this->buildTrend($year),
        ]);
    }

    public function rooms(Request $request): JsonResponse
    {
        [$year, $month, $selectedDate] = $this->resolvePeriod($request);
        $rooms = $this->buildRoomsPayload($selectedDate);

        return response()->json([
            'period' => [
                'year' => $year,
                'month' => $month,
                'month_name' => $selectedDate->translatedFormat('F'),
                'label' => $selectedDate->format('Y-m'),
            ],
            'rooms' => $rooms,
        ]);
    }

    private function resolvePeriod(Request $request): array
    {
        $now = Carbon::now();
        $year = (int) $request->integer('year', (int) $now->year);
        $month = (int) $request->integer('month', (int) $now->month);

        if ($month < 1 || $month > 12) {
            $month = (int) $now->month;
        }

        $selectedDate = Carbon::create($year, $month, 1);

        return [$year, $month, $selectedDate];
    }

    private function buildSummary(Carbon $previousDate): array
    {
        $totalFromMonitoring = (float) MonitoringEnergi::query()
            ->where('tahun', $previousDate->year)
            ->where('bulan', $previousDate->month)
            ->sum('konsumsi_kwh');

        $totalFromLaporan = (float) LaporanEnergi::query()
            ->where('tahun', $previousDate->year)
            ->where('bulan', $previousDate->month)
            ->sum('total_kwh');

        $totalEnergyLastMonth = $totalFromMonitoring > 0 ? $totalFromMonitoring : $totalFromLaporan;

        $activeRooms = (int) Ruangan::query()->where('status', 'digunakan')->count();
        $activeDevices = (int) DB::table('devices')->count();

        $latestControlPerRoom = DB::table('kontrol_listriks as kl')
            ->select('kl.ruangan_id', 'kl.aksi')
            ->join(
                DB::raw('(SELECT ruangan_id, MAX(created_at) as max_created FROM kontrol_listriks GROUP BY ruangan_id) as latest'),
                function ($join): void {
                    $join->on('latest.ruangan_id', '=', 'kl.ruangan_id')
                        ->on('latest.max_created', '=', 'kl.created_at');
                }
            )
            ->get();

        $trackedRooms = $latestControlPerRoom->count();
        $offCount = $latestControlPerRoom->where('aksi', 'off')->count();
        $efficiency = $trackedRooms > 0 ? round(($offCount / $trackedRooms) * 100, 1) : 0.0;

        return [
            'total_energy_last_month_kwh' => round($totalEnergyLastMonth, 2),
            'energy_efficiency_percent' => $efficiency,
            'active_rooms' => $activeRooms,
            'active_devices' => $activeDevices,
            'efficiency_formula' => 'persentase ruangan dengan status power terakhir OFF',
        ];
    }

    private function buildTrend(int $year): array
    {
        $monitoringRows = MonitoringEnergi::query()
            ->select('bulan', DB::raw('SUM(konsumsi_kwh) as total_kwh'))
            ->where('tahun', $year)
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $laporanRows = LaporanEnergi::query()
            ->select('bulan', DB::raw('SUM(total_kwh) as total_kwh'))
            ->where('tahun', $year)
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $fromMonitoring = (float) data_get($monitoringRows->get($m), 'total_kwh', 0);
            $fromLaporan = (float) data_get($laporanRows->get($m), 'total_kwh', 0);
            $value = $fromMonitoring > 0 ? $fromMonitoring : $fromLaporan;

            $months[] = [
                'month' => $m,
                'month_name' => Carbon::create($year, $m, 1)->translatedFormat('M'),
                'total_kwh' => round($value, 2),
            ];
        }

        return [
            'year' => $year,
            'monthly' => $months,
        ];
    }

    private function buildRoomsPayload(Carbon $selectedDate): array
    {
        $ruanganList = Ruangan::query()
            ->withCount('devices')
            ->orderBy('id')
            ->get();

        $consumptionFromMonitoring = MonitoringEnergi::query()
            ->select('ruangan_id', DB::raw('SUM(konsumsi_kwh) as total_kwh'))
            ->where('tahun', $selectedDate->year)
            ->where('bulan', $selectedDate->month)
            ->groupBy('ruangan_id')
            ->pluck('total_kwh', 'ruangan_id');

        $consumptionFromLaporan = LaporanEnergi::query()
            ->select('ruangan_id', DB::raw('SUM(total_kwh) as total_kwh'))
            ->where('tahun', $selectedDate->year)
            ->where('bulan', $selectedDate->month)
            ->groupBy('ruangan_id')
            ->pluck('total_kwh', 'ruangan_id');

        $latestPowerByRoom = DB::table('kontrol_listriks as kl')
            ->select('kl.ruangan_id', 'kl.aksi')
            ->join(
                DB::raw('(SELECT ruangan_id, MAX(created_at) as max_created FROM kontrol_listriks GROUP BY ruangan_id) as latest'),
                function ($join): void {
                    $join->on('latest.ruangan_id', '=', 'kl.ruangan_id')
                        ->on('latest.max_created', '=', 'kl.created_at');
                }
            )
            ->pluck('aksi', 'ruangan_id');

        return $ruanganList->map(function (Ruangan $ruangan) use ($consumptionFromMonitoring, $consumptionFromLaporan, $latestPowerByRoom): array {
            $monitoringValue = (float) ($consumptionFromMonitoring[$ruangan->id] ?? 0);
            $laporanValue = (float) ($consumptionFromLaporan[$ruangan->id] ?? 0);
            $consumption = $monitoringValue > 0 ? $monitoringValue : $laporanValue;

            $power = strtolower((string) ($latestPowerByRoom[$ruangan->id] ?? 'off')) === 'on' ? 'ON' : 'OFF';

            return [
                'id' => $ruangan->id,
                'nama_ruangan' => $ruangan->nama_ruangan,
                'lokasi' => $ruangan->lokasi,
                'status' => $ruangan->status,
                'devices_count' => (int) $ruangan->devices_count,
                'consumption_kwh' => round($consumption, 2),
                'power' => $power,
            ];
        })->values()->all();
    }
}
