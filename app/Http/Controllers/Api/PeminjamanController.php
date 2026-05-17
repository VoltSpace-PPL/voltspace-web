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

    public function show(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        if (! $this->canView($request->user(), $peminjaman)) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($peminjaman->load(['ruangan:id,kode,nama_ruangan', 'user:id,name,email', 'peninjau:id,name']));
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user->isMahasiswa()) {
            return response()->json(['message' => 'Hanya mahasiswa yang dapat mengajukan peminjaman.'], 403);
        }

        $data = $request->validate([
            'ruangan_id' => ['required', 'string', 'exists:ruangans,id'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'waktu_mulai' => ['required', 'date_format:H:i'],
            'waktu_selesai' => ['required', 'date_format:H:i', 'after:waktu_mulai'],
            'tujuan' => ['required', 'string', 'max:500'],
            'surat_peminjaman' => ['nullable', 'file', 'mimes:docx,doc,pdf,xlsx,xls', 'max:5120'],
        ], [
            'tanggal_mulai.after_or_equal' => 'Tanggal peminjaman tidak boleh memilih hari yang sudah lewat.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
        ]);

        if ($data['waktu_selesai'] > '20:00') {
            return response()->json(['message' => 'Waktu maksimal peminjaman adalah sampai pukul 20:00 (8 malam).'], 422);
        }

        $dMulai = Carbon::parse($data['tanggal_mulai'])->startOfDay();
        $dSelesai = Carbon::parse($data['tanggal_selesai'])->startOfDay();

        if (RoomScheduleGuard::peminjamanBlocks($data['ruangan_id'], $dMulai, $dSelesai, $data['waktu_mulai'], $data['waktu_selesai'], ['ditolak', 'dibatalkan'])) {
            return response()->json(['message' => 'Jadwal bentrok dengan peminjaman lain yang disetujui.'], 422);
        }
        if (RoomScheduleGuard::jadwalListrikBlocks($data['ruangan_id'], $dMulai, $dSelesai, $data['waktu_mulai'], $data['waktu_selesai'])) {
            return response()->json(['message' => 'Jadwal bentrok dengan jadwal perkuliahan reguler (Jadwal Listrik).'], 422);
        }

        $filePath = null;
        if ($request->hasFile('surat_peminjaman')) {
            $filePath = $request->file('surat_peminjaman')->store('surat_peminjaman', 'public');
        }

        $row = Peminjaman::create([
            'user_id' => $user->id,
            'ruangan_id' => $data['ruangan_id'],
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'waktu_mulai' => $data['waktu_mulai'],
            'waktu_selesai' => $data['waktu_selesai'],
            'tujuan' => $data['tujuan'],
            'surat_peminjaman' => $filePath,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Pengajuan berhasil dibuat.',
            'data' => $row->load(['ruangan:id,kode,nama_ruangan', 'user:id,name,email']),
        ], 201);
    }
}
