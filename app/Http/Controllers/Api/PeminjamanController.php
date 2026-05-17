<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\PeminjamanJadwalSyncService;
use App\Support\RoomScheduleGuard;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PeminjamanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $q = Peminjaman::query()->with(['ruangan:id,kode,nama_ruangan', 'user:id,name,email']);

        if ($user->isMahasiswa()) {
            $q->where('user_id', $user->id);
        } elseif (! $user->isStaffAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }
        if ($request->filled('ruangan_id')) {
            $q->where('ruangan_id', $request->string('ruangan_id'));
        }
        if ($request->filled('from')) {
            $q->whereDate('tanggal_selesai', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $q->whereDate('tanggal_mulai', '<=', $request->date('to'));
        }

        return response()->json($q->orderByDesc('created_at')->paginate(perPage: (int) $request->integer('per_page', 20)));
    }
}
