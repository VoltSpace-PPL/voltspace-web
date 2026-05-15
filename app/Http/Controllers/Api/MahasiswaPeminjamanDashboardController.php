<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MahasiswaPeminjamanDashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user->isMahasiswa()) {
            return response()->json(['message' => 'Hanya mahasiswa.'], 403);
        }

        $base = Peminjaman::query()->where('user_id', $user->id);

        $recent = (clone $base)
            ->with(['ruangan:id,kode,nama_ruangan'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return response()->json([
            'total_request' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'approved' => (clone $base)->where('status', 'disetujui')->count(),
            'rejected' => (clone $base)->where('status', 'ditolak')->count(),
            'recent_booking' => $recent,
        ]);
    }
}
