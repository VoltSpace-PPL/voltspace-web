<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalListrik;
use App\Models\Ruangan;
use App\Services\XlsxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalListrikExcelController extends Controller
{
    public function downloadTemplate()
    {
        return XlsxService::download('template_jadwal_listrik.xlsx', function ($spreadsheet): void {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray([
                ['ruangan_kode', 'tanggal_mulai', 'tanggal_selesai', 'selected_days', 'start_time', 'end_time', 'automation_action', 'schedule_status', 'device_id'],
                ['RM-001', '2026-05-01', '2026-12-31', 'monday,wednesday', '08:00', '17:00', 'on', 'active', ''],
            ], null, 'A1', true);
        });
    }

    public function import(Request $request): JsonResponse
    {
        if (! $request->user()->isStaffAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls']]);
        $path = $request->file('file')->getRealPath();
        if (! $path) {
            return response()->json(['message' => 'File tidak valid.'], 422);
        }
        $rows = XlsxService::readRows($path);
        $header = array_map(fn ($h) => is_string($h) ? strtolower(trim($h)) : $h, $rows[0] ?? []);
        $idx = array_flip($header);
        $need = ['ruangan_kode', 'start_time', 'end_time', 'automation_action'];
        foreach ($need as $c) {
            if (! isset($idx[$c])) {
                return response()->json(['message' => 'Kolom wajib tidak ada: '.$c], 422);
            }
        }

        $created = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach (array_slice($rows, 1) as $n => $row) {
                $line = $n + 2;
                if (! array_filter($row, fn ($x) => $x !== null && $x !== '')) {
                    continue;
                }
                $kode = trim((string) ($row[$idx['ruangan_kode']] ?? ''));
                $ruangan = Ruangan::query()
                    ->where(function ($q) use ($kode): void {
                        $q->where('kode', $kode)->orWhere('id', $kode);
                    })
                    ->first();
                if (! $ruangan) {
                    $errors[] = "Baris {$line}: ruangan tidak ditemukan";

                    continue;
                }
                $daysRaw = isset($idx['selected_days']) ? trim((string) ($row[$idx['selected_days']] ?? '')) : '';
                $selected = [];
                if ($daysRaw !== '') {
                    $rawArray = array_filter(array_map('trim', explode(',', strtolower($daysRaw))));
                    $allowed = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    $selected = array_values(array_intersect($rawArray, $allowed));
                    if ($selected === []) {
                        $errors[] = "Baris {$line}: selected_days tidak valid";
                        continue;
                    }
                } else {
                    $selected = null;
                }
                $start = trim((string) ($row[$idx['start_time']] ?? ''));
                $end = trim((string) ($row[$idx['end_time']] ?? ''));
                $action = strtolower(trim((string) ($row[$idx['automation_action']] ?? '')));
                if (! in_array($action, ['on', 'off'], true)) {
                    $errors[] = "Baris {$line}: automation_action harus on/off";

                    continue;
                }
                $status = isset($idx['schedule_status']) ? strtolower(trim((string) ($row[$idx['schedule_status']] ?? 'active'))) : 'active';
                if (! in_array($status, ['active', 'inactive'], true)) {
                    $status = 'active';
                }
                $tMulai = isset($idx['tanggal_mulai']) ? trim((string) ($row[$idx['tanggal_mulai']] ?? '')) : null;
                $tSelesai = isset($idx['tanggal_selesai']) ? trim((string) ($row[$idx['tanggal_selesai']] ?? '')) : null;
                $deviceId = isset($idx['device_id']) ? trim((string) ($row[$idx['device_id']] ?? '')) : '';
                $deviceId = $deviceId === '' ? null : (int) $deviceId;

                JadwalListrik::create([
                    'ruangan_id' => $ruangan->id,
                    'device_id' => $deviceId,
                    'selected_days' => $selected,
                    'start_time' => strlen($start) === 5 ? $start : substr($start, 0, 5),
                    'end_time' => strlen($end) === 5 ? $end : substr($end, 0, 5),
                    'automation_action' => $action,
                    'schedule_status' => $status,
                    'waktu_mulai' => strlen($start) === 5 ? $start : substr($start, 0, 5),
                    'waktu_selesai' => strlen($end) === 5 ? $end : substr($end, 0, 5),
                    'status_listrik' => $action === 'on' ? 'nyala' : 'mati',
                    'tanggal_mulai' => $tMulai !== '' && $tMulai !== null ? $tMulai : null,
                    'tanggal_selesai' => $tSelesai !== '' && $tSelesai !== null ? $tSelesai : null,
                ]);
                $created++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['message' => 'Import gagal: '.$e->getMessage()], 422);
        }

        return response()->json(['message' => 'Import selesai.', 'created' => $created, 'errors' => $errors]);
    }
}
