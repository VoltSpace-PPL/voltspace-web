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

    public function toggle(Request $request): JsonResponse
{
    $device = Device::first(); 

    if (!$device) {
        return response()->json([
            'message' => 'Device tidak ditemukan'
        ], 404);
    }

    $device->relay = $request->relay;
    $device->save();

    return response()->json([
        'success' => true,
        'relay' => $device->relay
    ]);
}
    
}

