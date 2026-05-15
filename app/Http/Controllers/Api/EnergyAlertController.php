<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EnergyAlertSetting;
use App\Services\EnergyAlertService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnergyAlertController extends Controller
{
    public function __construct(
        private readonly EnergyAlertService $energyAlertService,
    ) {}

    public function settings(Request $request): JsonResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Hanya super_admin yang dapat mengatur ambang peringatan energi global.'], 403);
        }

        return response()->json(EnergyAlertSetting::current());
    }

    public function updateSettings(Request $request): JsonResponse
    {
        if (! $request->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Hanya super_admin yang dapat mengatur ambang peringatan energi global.'], 403);
        }

        $data = $request->validate([
            'high_usage_threshold_kwh' => ['required', 'numeric', 'min:0'],
            'peak_demand_limit_kw' => ['required', 'numeric', 'min:0'],
        ]);

        $setting = EnergyAlertSetting::current();
        $setting->update([
            'high_usage_threshold_kwh' => $data['high_usage_threshold_kwh'],
            'peak_demand_limit_kw' => $data['peak_demand_limit_kw'],
            'updated_by' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Pengaturan disimpan.', 'data' => $setting->fresh()]);
    }

    /**
     * Peringatan dihitung real-time (tidak disimpan). FE cukup polling endpoint ini.
     */
    public function alerts(Request $request): JsonResponse
    {
        if (! $request->user()->isStaffAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $year = (int) $request->integer('tahun', (int) Carbon::now()->year);
        $month = (int) $request->integer('bulan', (int) Carbon::now()->month);

        return response()->json($this->energyAlertService->buildAlerts($year, $month));
    }
}
