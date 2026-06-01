<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\EnergyAlertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InAppNotificationController extends Controller
{
    public function __construct(
        private readonly EnergyAlertService $energyAlertService,
    ) {}

    /**
     * Notifikasi in-app: peminjaman (DB) + alert energi (dihitung, tidak disimpan).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $items = [];

        $pq = Peminjaman::query()->with('ruangan:id,kode,nama_ruangan')->orderByDesc('updated_at')->limit(15);
        if ($user->isMahasiswa()) {
            $pq->where('user_id', $user->id);
        } elseif (! $user->isStaffAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        foreach ($pq->get() as $p) {
            $items[] = [
                'type' => 'peminjaman',
                'title' => 'Peminjaman '.$p->status,
                'body' => ($p->ruangan?->nama_ruangan ?? $p->ruangan_id).' — '.$p->tanggal_mulai?->format('Y-m-d'),
                'meta' => ['peminjaman_id' => $p->id, 'status' => $p->status],
                'created_at' => $p->updated_at?->toIso8601String(),
            ];
        }

        $energyPayload = ['alerts' => []];
        if ($user->isStaffAdmin()) {
            $built = $this->energyAlertService->buildAlerts();
            $energyPayload['alerts'] = array_map(static function (array $alert): array {
                return [
                    'type' => 'energy',
                    'severity' => $alert['severity'],
                    'status' => $alert['status'],
                    'title' => $alert['status'] === 'exceeded' ? 'Konsumsi melebihi batas' : 'Konsumsi hampir melebihi batas',
                    'body' => $alert['message'],
                    'meta' => [
                        'ruangan_id' => $alert['ruangan_id'],
                        'nama_ruangan' => $alert['nama_ruangan'],
                        'total_kwh' => $alert['total_kwh'],
                        'threshold_kwh' => $alert['threshold_kwh'],
                    ],
                    'created_at' => now()->toIso8601String(),
                ];
            }, $built['alerts']);
        }

        return response()->json([
            'peminjaman' => $items,
            'energy_alerts' => $energyPayload['alerts'],
        ]);
    }
}
