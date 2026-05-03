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
    public function status(Device $device): JsonResponse
    {
        try {
            $res = Http::timeout(5)->get($this->url($device, 'status'));

            return response()->json($res->json());
        } catch (\Exception $e) {
            return response()->json([
                'online' => false,
                'relay' => 'OFF',
                'voltage' => 0,
                'current' => 0,
                'power' => 0,
                'energy' => 0,
            ]);
        }
    }

    public function on(Request $request, Device $device): JsonResponse
    {
        return $this->sendCommand($request, $device, 'on');
    }

    public function off(Request $request, Device $device): JsonResponse
    {
        return $this->sendCommand($request, $device, 'off');
    }

    public function toggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'integer', 'exists:devices,id'],
            'aksi' => ['required', 'in:on,off'],
        ]);

        $device = Device::findOrFail($data['device_id']);

        return $this->sendCommand($request, $device, $data['aksi']);
    }

    private function sendCommand(Request $request, Device $device, string $aksi): JsonResponse
    {
        $url = $this->url($device, $aksi);

        try {
            $res = Http::timeout(5)->get($url);
        } catch (ConnectionException $e) {
            return response()->json([
                'message' => 'Gagal terhubung ke device IoT.',
                'success' => false,
            ], 502);
        }

        try {
            KontrolListrik::create([
                'user_id' => optional($request->user())->id,
                'ruangan_id' => $device->ruangan_id,
                'device_id' => $device->id,
                'aksi' => $aksi,
            ]);
        } catch (\Exception $e) {
        }

        return response()->json([
            'message' => 'Perintah berhasil dikirim.',
            'success' => $res->successful(),
            'device' => $device->only(['id', 'name', 'type', 'ip_address', 'ruangan_id']),
            'iot_request' => [
                'url' => $url,
                'status' => $res->status(),
                'ok' => $res->successful(),
            ],
        ]);
    }

    private function url(Device $device, string $endpoint): string
    {
        $ip = trim($device->ip_address);

        if (!str_starts_with($ip, 'http://') && !str_starts_with($ip, 'https://')) {
            $ip = 'http://' . $ip;
        }

        return rtrim($ip, '/') . '/' . ltrim($endpoint, '/');
    }
}