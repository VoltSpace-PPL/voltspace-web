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

        return response()->json($query->latest()->get());
    }

    public function store(StoreRuanganRequest $request): JsonResponse
    {
        $ruangan = Ruangan::create([
            'id'           => $request->filled('id') ? $request->input('id') : null,
            'nama_ruangan' => $request->input('nama_ruangan'),
            'kapasitas'    => $request->integer('kapasitas'),
            'lantai'       => $request->integer('lantai', 1),
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
            'nama_ruangan' => ['sometimes', 'string', 'max:255', 'unique:ruangans,nama_ruangan,'.$ruangan->id.',id'],
            'kapasitas'    => ['sometimes', 'integer', 'min:0'],
            'lantai'       => ['sometimes', 'integer', 'min:1', 'max:3'],
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
