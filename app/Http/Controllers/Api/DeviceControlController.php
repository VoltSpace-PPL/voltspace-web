<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\KontrolListrik;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class DeviceControlController extends Controller
{
    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'integer', 'exists:devices,id'],
            'aksi' => ['required', 'in:on,off'],
        ]);

        $device = Device::query()->findOrFail($data['device_id']);

        $endpoint = $data['aksi'] === 'on' ? 'on' : 'off';
        $url = rtrim((string) $device->ip_address, '/').'/'.$endpoint;
        try {
            $res = Http::timeout(5)->get($url);
        } catch (ConnectionException $e) {
            return response()->json([
                'message' => 'Gagal terhubung ke device IoT.',
                'device' => $device->only(['id', 'name', 'type', 'ip_address', 'ruangan_id']),
                'iot_request' => [
                    'url' => $url,
                    'ok' => false,
                    'status' => null,
                    'error' => 'connection_failed',
                ],
            ], 502);
        }

        KontrolListrik::create([
            'user_id' => $request->user()->id,
            'ruangan_id' => $device->ruangan_id,
            'device_id' => $device->id,
            'aksi' => $data['aksi'],
        ]);

        return response()->json([
            'message' => 'Perintah berhasil dikirim.',
            'device' => $device->only(['id', 'name', 'type', 'ip_address', 'ruangan_id']),
            'iot_request' => [
                'url' => $url,
                'status' => $res->status(),
                'ok' => $res->successful(),
            ],
        ]);
    }
}

