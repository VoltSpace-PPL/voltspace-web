<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRuanganRequest;
use App\Models\Ruangan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Ruangan::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', 'like', '%'.$request->input('lokasi').'%');
        }

        return response()->json($query->latest()->get());
    }

    public function store(StoreRuanganRequest $request): JsonResponse
    {
        $ruangan = Ruangan::create([
            'id'           => $request->filled('id') ? $request->input('id') : null,
            'nama_ruangan' => $request->input('nama_ruangan'),
            'lokasi'       => $request->input('lokasi'),
            'kapasitas'    => $request->integer('kapasitas'),
            'status'       => $request->input('status', 'tersedia'),
        ]);

        return response()->json([
            'message' => 'Ruangan berhasil dibuat.',
            'data' => $ruangan,
        ], 201);
    }

    public function update(Request $request, Ruangan $ruangan): JsonResponse
    {
        $data = $request->validate([
            'nama_ruangan' => ['sometimes', 'string', 'max:255'],
            'lokasi'       => ['sometimes', 'string', 'max:255'],
            'kapasitas'    => ['sometimes', 'integer', 'min:0'],
            'status'       => ['sometimes', 'in:tersedia,digunakan,dipesan'],
        ]);

        $ruangan->update($data);

        return response()->json([
            'message' => 'Ruangan berhasil diupdate.',
            'data' => $ruangan->fresh(),
        ]);
    }

    public function destroy(Ruangan $ruangan): JsonResponse
    {
        $ruangan->delete();

        return response()->json([
            'message' => 'Ruangan berhasil dihapus.',
        ]);
    }
}
