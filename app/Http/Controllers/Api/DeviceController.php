<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Device::query();

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->string('ruangan_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        return response()->json($query->latest()->get());
    }


    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'ip_address' => ['required', 'string', 'max:255'],
            'ruangan_id' => ['nullable', 'string', 'exists:ruangans,id'],
        ]);

        $device = Device::create($data);

        return response()->json([
            'message' => 'Device berhasil dibuat.',
            'data' => $device,
        ], 201);
    }

    public function update(Request $request, Device $device): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:255'],
            'ip_address' => ['sometimes', 'string', 'max:255'],
            'ruangan_id' => ['sometimes', 'string', 'exists:ruangans,id'],
        ]);

        $device->update($data);

        return response()->json([
            'message' => 'Device berhasil diupdate.',
            'data' => $device->fresh(),
        ]);
    }

    public function destroy(Device $device): JsonResponse
    {
        $device->delete();

        return response()->json([
            'message' => 'Device berhasil dihapus.',
        ]);
    }
}

