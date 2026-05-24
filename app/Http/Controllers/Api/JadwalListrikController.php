<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalListrik;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JadwalListrikController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = JadwalListrik::query();

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->input('ruangan_id'));
        }

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->integer('device_id'));
        }

        if ($request->filled('schedule_status')) {
            $query->where('schedule_status', $request->string('schedule_status'));
        }

        if ($request->filled('from') && $request->filled('to')) {
            $from = $request->date('from');
            $to = $request->date('to');
            $query->where(function ($q) use ($from, $to) {
                $q->where(function($q) use ($to) {
                    $q->whereNull('tanggal_mulai')->orWhereDate('tanggal_mulai', '<=', $to);
                })->where(function($q) use ($from) {
                    $q->whereNull('tanggal_selesai')->orWhereDate('tanggal_selesai', '>=', $from);
                });
            });
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ruangan_id' => ['required', 'string', 'exists:ruangans,id'],
            'device_id' => ['nullable', 'integer', 'exists:devices,id'],
            'selected_days' => ['nullable', 'array'],
            'selected_days.*' => ['string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'automation_action' => ['required', 'in:on,off'],
            'schedule_status' => ['nullable', 'in:active,inactive'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
        ]);

        $jadwal = JadwalListrik::create([
            'ruangan_id' => $data['ruangan_id'],
            'device_id' => $data['device_id'] ?? null,
            'selected_days' => $data['selected_days'] ?? null,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'automation_action' => $data['automation_action'],
            'schedule_status' => $data['schedule_status'] ?? 'active',
            'waktu_mulai' => $data['start_time'],
            'waktu_selesai' => $data['end_time'],
            'status_listrik' => $data['automation_action'] === 'on' ? 'nyala' : 'mati',
            'tanggal_mulai' => $data['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
        ]);

        return response()->json([
            'message' => 'Jadwal listrik berhasil dibuat.',
            'data' => $jadwal,
        ], 201);
    }

    public function update(Request $request, JadwalListrik $jadwal): JsonResponse
    {
        $data = $request->validate([
            'ruangan_id' => ['sometimes', 'string', 'exists:ruangans,id'],
            'device_id' => ['nullable', 'integer', 'exists:devices,id'],
            'selected_days' => ['nullable', 'array'],
            'selected_days.*' => ['string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i'],
            'automation_action' => ['sometimes', 'in:on,off'],
            'schedule_status' => ['sometimes', 'in:active,inactive'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
        ]);

        if (array_key_exists('start_time', $data)) {
            $data['waktu_mulai'] = $data['start_time'];
        }

        if (array_key_exists('end_time', $data)) {
            $data['waktu_selesai'] = $data['end_time'];
        }

        if (array_key_exists('automation_action', $data)) {
            $data['status_listrik'] = $data['automation_action'] === 'on' ? 'nyala' : 'mati';
        }

        $jadwal->update($data);

        return response()->json([
            'message' => 'Jadwal listrik berhasil diupdate.',
            'data' => $jadwal->fresh(),
        ]);
    }

    public function iotCommand(Request $request): JsonResponse
    {
        $now = Carbon::now('Asia/Jakarta');

        $today = strtolower($now->format('l')); 
        $currentTime = $now->format('H:i');
        $todayDate = $now->toDateString();

        $query = JadwalListrik::query()
            ->where('schedule_status', 'active')
            ->where(function ($q) use ($todayDate) {
                $q->whereNull('tanggal_mulai')
                ->orWhereDate('tanggal_mulai', '<=', $todayDate);
            })
            ->where(function ($q) use ($todayDate) {
                $q->whereNull('tanggal_selesai')
                ->orWhereDate('tanggal_selesai', '>=', $todayDate);
            });

    // Opsional: kalau ESP32 kirim device_id
    if ($request->filled('device_id')) {
        $query->where(function ($q) use ($request) {
            $q->where('device_id', $request->integer('device_id'))
              ->orWhereNull('device_id');
        });
    }

    if ($request->filled('ruangan_id')) {
        $query->where('ruangan_id', $request->input('ruangan_id'));
    }

    $jadwals = $query->latest()->get();

    $relay = 0;
    $activeSchedule = null;

    foreach ($jadwals as $jadwal) {
        $selectedDays = $jadwal->selected_days;

        if (is_string($selectedDays)) {
            $selectedDays = json_decode($selectedDays, true);
        }

        if (!is_array($selectedDays)) {
            $selectedDays = [];
        }

        if (!empty($selectedDays) && !in_array($today, $selectedDays, true)) {
            continue;
        }

        $start = $jadwal->start_time ?? $jadwal->waktu_mulai;
        $end = $jadwal->end_time ?? $jadwal->waktu_selesai;

        if (!$start || !$end) {
            continue;
        }

        if ($this->isTimeInRange($currentTime, $start, $end)) {
            $activeSchedule = $jadwal;
            $relay = $jadwal->automation_action === 'on' ? 1 : 0;
            break;
        }
    }

    return response()->json([
        'relay' => $relay,
        'state' => $relay ? 'ON' : 'OFF',
        'message' => $activeSchedule ? 'Jadwal aktif ditemukan' : 'Tidak ada jadwal aktif',
        'active_schedule_id' => $activeSchedule?->id,
        'automation_action' => $activeSchedule?->automation_action,
        'server_time' => $now->format('Y-m-d H:i:s'),
    ]);
}

private function isTimeInRange(string $now, string $start, string $end): bool
{
    $now = substr($now, 0, 5);
    $start = substr($start, 0, 5);
    $end = substr($end, 0, 5);

    // Contoh normal: 08:00 - 17:00
    if ($start <= $end) {
        return $now >= $start && $now < $end;
    }

    // Contoh lewat tengah malam: 22:00 - 05:00
    return $now >= $start || $now < $end;
}
    public function destroy(JadwalListrik $jadwal): JsonResponse
    {
        $jadwal->delete();

        return response()->json([
            'message' => 'Jadwal listrik berhasil dihapus.',
        ]);
    }
}
