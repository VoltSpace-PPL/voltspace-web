<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalListrik;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JadwalListrikController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = JadwalListrik::query();

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->integer('ruangan_id'));
        }

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->integer('device_id'));
        }

        if ($request->filled('schedule_status')) {
            $query->where('schedule_status', $request->string('schedule_status'));
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ruangan_id' => ['required', 'string', 'exists:ruangans,id'],
            'device_id' => ['nullable', 'integer', 'exists:devices,id'],
            'selected_days' => ['required', 'array', 'min:1'],
            'selected_days.*' => ['string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'automation_action' => ['required', 'in:on,off'],
            'schedule_status' => ['nullable', 'in:active,inactive'],
        ]);

        $jadwal = JadwalListrik::create([
            'ruangan_id' => $data['ruangan_id'],
            'device_id' => $data['device_id'] ?? null,
            'selected_days' => $data['selected_days'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'automation_action' => $data['automation_action'],
            'schedule_status' => $data['schedule_status'] ?? 'active',
            'waktu_mulai' => $data['start_time'],
            'waktu_selesai' => $data['end_time'],
            'status_listrik' => $data['automation_action'] === 'on' ? 'nyala' : 'mati',
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
            'selected_days' => ['sometimes', 'array', 'min:1'],
            'selected_days.*' => ['string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i'],
            'automation_action' => ['sometimes', 'in:on,off'],
            'schedule_status' => ['sometimes', 'in:active,inactive'],
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

    public function destroy(JadwalListrik $jadwal): JsonResponse
    {
        $jadwal->delete();

        return response()->json([
            'message' => 'Jadwal listrik berhasil dihapus.',
        ]);
    }
}

