<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\PeminjamanJadwalSyncService;
use App\Services\XlsxService;
use App\Support\RoomScheduleGuard;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PeminjamanController extends Controller
{
    public function __construct(
        private readonly PeminjamanJadwalSyncService $jadwalSync,
    ) {}

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
            'ruangan_id'      => ['required', 'string', 'exists:ruangans,id'],
            'tanggal_mulai'   => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'waktu_mulai'     => ['required', 'date_format:H:i'],
            'waktu_selesai'   => ['required', 'date_format:H:i', 'after:waktu_mulai'],
            'tujuan'          => ['required', 'string', 'max:500'],
            'surat_peminjaman' => ['nullable', 'file', 'mimes:docx,doc,pdf,xlsx,xls', 'max:5120'],
        ], [
            'tanggal_mulai.after_or_equal' => 'Tanggal peminjaman tidak boleh memilih hari yang sudah lewat.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
        ]);

        // Aturan H-3: pengajuan wajib dilakukan minimal 3 hari sebelum tanggal mulai
        $dMulai = Carbon::parse($data['tanggal_mulai'])->startOfDay();
        if ($dMulai->lt(Carbon::today()->addDays(3))) {
            return response()->json(['message' => 'Pengajuan peminjaman harus dilakukan paling lambat H-3 (3 hari sebelum tanggal kegiatan).'], 422);
        }

        if ($data['waktu_selesai'] > '20:00') {
            return response()->json(['message' => 'Waktu maksimal peminjaman adalah sampai pukul 20:00 (8 malam).'], 422);
        }

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
            'user_id'          => $user->id,
            'ruangan_id'       => $data['ruangan_id'],
            'tanggal_mulai'    => $data['tanggal_mulai'],
            'tanggal_selesai'  => $data['tanggal_selesai'],
            'waktu_mulai'      => $data['waktu_mulai'],
            'waktu_selesai'    => $data['waktu_selesai'],
            'tujuan'           => $data['tujuan'],
            'surat_peminjaman' => $filePath,
            'status'           => 'pending',
        ]);

        return response()->json([
            'message' => 'Pengajuan berhasil dibuat.',
            'data'    => $row->load(['ruangan:id,kode,nama_ruangan', 'user:id,name,email']),
        ], 201);
    }

    public function approve(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        if (! $request->user()->isStaffAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }
        if ($peminjaman->status !== 'pending') {
            return response()->json(['message' => 'Hanya pengajuan pending yang dapat disetujui.'], 422);
        }

        $dMulai = Carbon::parse($peminjaman->tanggal_mulai)->startOfDay();
        $dSelesai = Carbon::parse($peminjaman->tanggal_selesai)->startOfDay();
        $wm = substr((string) $peminjaman->waktu_mulai, 0, 5);
        $ws = substr((string) $peminjaman->waktu_selesai, 0, 5);

        if (RoomScheduleGuard::peminjamanBlocks($peminjaman->ruangan_id, $dMulai, $dSelesai, $wm, $ws, ['ditolak', 'dibatalkan', 'pending'], $peminjaman->id)) {
            return response()->json(['message' => 'Tidak dapat menyetujui karena bentrok dengan peminjaman lain.'], 422);
        }
        if (RoomScheduleGuard::jadwalListrikBlocks($peminjaman->ruangan_id, $dMulai, $dSelesai, $wm, $ws)) {
            return response()->json(['message' => 'Tidak dapat menyetujui karena bentrok dengan jadwal perkuliahan reguler (Jadwal Listrik).'], 422);
        }

        $peminjaman->update([
            'status'       => 'disetujui',
            'reviewed_at'  => now(),
            'reviewed_by'  => $request->user()->id,
            'catatan_admin' => null,
        ]);

        $jadwal = $this->jadwalSync->syncOnApprove($peminjaman->fresh());

        return response()->json([
            'message'        => 'Disetujui. Jadwal listrik otomatis dibuat dan lampu diset nyala.',
            'data'           => $peminjaman->fresh()->load(['ruangan:id,kode,nama_ruangan', 'user:id,name,email']),
            'jadwal_listrik' => $jadwal,
        ]);
    }

    public function reject(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        if (! $request->user()->isStaffAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }
        if ($peminjaman->status !== 'pending') {
            return response()->json(['message' => 'Hanya pengajuan pending yang dapat ditolak.'], 422);
        }

        $data = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $peminjaman->update([
            'status'        => 'ditolak',
            'catatan_admin' => $data['catatan_admin'] ?? null,
            'reviewed_at'   => now(),
            'reviewed_by'   => $request->user()->id,
        ]);

        return response()->json(['message' => 'Ditolak.', 'data' => $peminjaman->fresh()]);
    }

    /**
     * Pembatalan peminjaman.
     * - Mahasiswa: TIDAK dapat membatalkan (setelah disetujui, hanya admin yang bisa membatalkan).
     * - Admin: hanya dapat membatalkan peminjaman yang sudah DISETUJUI (kondisi darurat/prioritas institusi),
     * - Mahasiswa: Dapat membatalkan hingga H-3 sebelum jadwal.
     * - Admin: dapat membatalkan peminjaman yang sudah DISETUJUI, maksimal H-1.
     */
    public function cancel(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        $user = $request->user();

        if (in_array($peminjaman->status, ['dibatalkan', 'ditolak'])) {
            return response()->json(['message' => 'Pengajuan sudah dalam status dibatalkan atau ditolak.'], 422);
        }

        $dMulai  = Carbon::parse($peminjaman->tanggal_mulai)->startOfDay();
        $hariIni = Carbon::today();

        if ($user->isStaffAdmin()) {
            // Admin hanya bisa membatalkan peminjaman yang sudah disetujui
            if ($peminjaman->status !== 'disetujui') {
                return response()->json(['message' => 'Pembatalan hanya dapat dilakukan pada peminjaman yang sudah disetujui.'], 422);
            }
            if ($hariIni->gt($dMulai->copy()->subDays(1))) {
                return response()->json(['message' => 'Pembatalan hanya dapat dilakukan paling lambat H-1 sebelum jadwal penggunaan.'], 422);
            }
        } elseif ($user->isMahasiswa() && $peminjaman->user_id === $user->id) {
            // Mahasiswa maksimal H-3
            if ($hariIni->gt($dMulai->copy()->subDays(3))) {
                return response()->json(['message' => 'Pembatalan hanya dapat dilakukan paling lambat H-3 sebelum jadwal penggunaan.'], 422);
            }
        } else {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $data = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $updateData = ['status' => 'dibatalkan'];
        if ($user->isStaffAdmin()) {
            $updateData['reviewed_by'] = $user->id;
            $updateData['reviewed_at'] = now();
            if ($request->has('catatan_admin')) {
                $updateData['catatan_admin'] = $data['catatan_admin'];
            }
        }

        $peminjaman->update($updateData);
        $this->jadwalSync->deleteJadwalForPeminjaman($peminjaman->id);

        return response()->json(['message' => 'Peminjaman berhasil dibatalkan.', 'data' => $peminjaman->fresh()]);
    }

    public function downloadTemplate(Request $request)
    {
        $downloadName = XlsxService::filenameFromRequest($request, 'Template_Surat_Peminjaman.xlsx');
        $path = storage_path('app/templates/template_surat_peminjaman.xlsx');

        if (is_readable($path) && XlsxService::isSpreadsheetFile($path)) {
            return XlsxService::downloadFromPath($path, 'Template_Surat_Peminjaman.xlsx', $downloadName);
        }

        return XlsxService::download('Template_Surat_Peminjaman.xlsx', function ($spreadsheet): void {
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(25);
            $sheet->getColumnDimension('B')->setWidth(3);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(25);

            $borderThin = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                    ],
                ],
            ];
            $borderOutline = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => 'thin',
                    ],
                ],
            ];

            // Outer border for the whole document
            $sheet->getStyle('A1:F57')->applyFromArray($borderOutline);

            // FORMULIR PEMINJAMAN RUANGAN
            $sheet->mergeCells('A1:F1');
            $sheet->setCellValue('A1', 'FORMULIR PEMINJAMAN RUANGAN');
            $sheet->getStyle('A1')->getFont()->setBold(true);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A1:F1')->applyFromArray($borderOutline);

            $sheet->setCellValue('A3', 'Tanggal Kegiatan');   $sheet->setCellValue('B3', ':');
            $sheet->setCellValue('A4', 'Waktu Kegiatan');     $sheet->setCellValue('B4', ':');
            $sheet->setCellValue('A5', 'Tempat Kegiatan');    $sheet->setCellValue('B5', ':');
            $sheet->setCellValue('A6', 'Rincian Kegiatan');   $sheet->setCellValue('B6', ':'); $sheet->setCellValue('C6', 'Study Group');
            
            $sheet->mergeCells('C3:F3');
            $sheet->mergeCells('C4:F4');
            $sheet->mergeCells('C5:F5');
            $sheet->mergeCells('C6:F6');
            $sheet->getStyle('A3:F6')->applyFromArray($borderThin);

            $sheet->setCellValue('A8', 'Dengan ini pula saya bertanggung jawab atas :');
            $sheet->setCellValue('A9', '1. Barang - barang yang ada di tempat yang dipinjam');
            $sheet->setCellValue('A10', '2. Fasilitas yang ada di tempat yang dipinjam');
            $sheet->setCellValue('A11', '3. Menjaga kebersihan di tempat yang dipinjam seperti sebelum digunakan');
            $sheet->setCellValue('A12', '4. Menggunakan fasilitas ruangan yang dipinjam sebagai mana mestinya');
            $sheet->setCellValue('A13', '5. Mengganti apabila ada fasilitas ditempat yang dipinjam mengalami kerusakan atau kehilangan');
            $sheet->setCellValue('A14', '6. Apabila melanggar peraturan ini saya bersedia untuk tidak lagi diberikan izin meminjam ruangan dikemudian hari dan dikenakan sanksi sesuai peraturan yang berlaku');
            $sheet->setCellValue('A15', '7. Tidak akan melebihi waktu yang sudah ditentukan (senin - kamis 18.00 - 19.00, sabtu 06.00 - 17.00)');
            $sheet->setCellValue('A16', '8. Melampirkan proposal kegiatan');
            $sheet->setCellValue('A17', '9. Melampirkan Pakta Integritas');

            $sheet->setCellValue('A19', 'Catatan');
            $sheet->getStyle('A19')->getFont()->setBold(true);
            $sheet->setCellValue('A20', '1. Untuk kegiatan di hari minggu atau tanggal merah tidak ada peminjaman fasilitas');
            $sheet->setCellValue('A22', '2. Jika formulir sudah ditandatangani harap diperbanyak dan di lengkapi dengan stempel logistik lalu diserahkan ke staf');
            $sheet->setCellValue('A23', '3. Peminjaman tidak boleh mendadak maksimal H-3 sebelum acara dilaksanakan');
            $sheet->setCellValue('A24', '4. Tidak melebihi waktu yang telah ditentukan');
            $sheet->setCellValue('A25', '5. Dilengkapi stempel Ormawa/UKM di kolom pemohon');
            $sheet->setCellValue('A26', '6. Khusus UKM harus ada tanda tangan dari pihak DITMAWA');
            $sheet->setCellValue('A27', '7. Kedepankan etika');

            // PAKTA INTEGRITAS
            $sheet->mergeCells('A30:F30');
            $sheet->setCellValue('A30', 'PAKTA INTEGRITAS');
            $sheet->getStyle('A30')->getFont()->setBold(true);
            $sheet->getStyle('A30')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A30:F30')->applyFromArray($borderOutline);

            $sheet->setCellValue('A32', 'Saya yang bertanda tangan di bawah ini:');
            $sheet->setCellValue('A33', 'Nama');              $sheet->setCellValue('B33', ':');
            $sheet->setCellValue('A34', 'NIM');               $sheet->setCellValue('B34', ':');
            $sheet->setCellValue('A35', 'Jabatan');           $sheet->setCellValue('B35', ':');
            $sheet->setCellValue('A36', 'No. Kontak/Email');  $sheet->setCellValue('B36', ':');

            $sheet->mergeCells('C33:F33');
            $sheet->mergeCells('C34:F34');
            $sheet->mergeCells('C35:F35');
            $sheet->mergeCells('C36:F36');
            $sheet->getStyle('A33:F36')->applyFromArray($borderThin);

            $sheet->setCellValue('A38', 'Dalam rangka menyelenggarakan kegiatan ____________________________________ dengan ini menyatakan bahwa:');
            $sheet->setCellValue('A39', '1. Akan menjaga dan menjunjung tinggi nama baik (citra) almamater Universitas Telkom;');
            $sheet->setCellValue('A40', '2. Akan mematuhi Kode Etik Mahasiswa Universitas Telkom,');
            $sheet->setCellValue('A41', '3. Tidak akan melakukan aktivitas/tindakan yang kontraproduktif dengan cita-cita Universitas Telkom;');
            $sheet->setCellValue('A42', '4. Akan melaksanakan kegiatan tepat waktu sesuai dengan jam pelaksanaan kegiatan;');
            $sheet->setCellValue('A43', '5. Akan menjaga kebersihan, ketertiban, dan keamanan;');
            $sheet->setCellValue('A44', '6. Memperhatikan aspek-aspek keselamatan serta melaksanakan aturan dan prosedur operasional yang berlaku;');
            $sheet->setCellValue('A45', '7. Tidak mengganggu kegiatan akademik.');

            $sheet->setCellValue('A47', 'Apabila saya melanggar hal-hal yang menjadi komitmen dalam Pakta Integritas ini, saya bersedia dikenakan sanksi sesuai');
            $sheet->setCellValue('A48', 'peraturan yang berlaku di Universitas Telkom.');

            $sheet->setCellValue('B51', 'TTD Peminjam');
            $sheet->getStyle('B51')->getAlignment()->setHorizontal('center');
            $sheet->setCellValue('F51', 'TTD Dosen');
            $sheet->getStyle('F51')->getAlignment()->setHorizontal('center');

            $sheet->setCellValue('B56', '(.....................................)');
            $sheet->getStyle('B56')->getAlignment()->setHorizontal('center');
            $sheet->setCellValue('F56', '(.....................................)');
            $sheet->getStyle('F56')->getAlignment()->setHorizontal('center');
        }, $downloadName);
    }

    public function previewSurat(Request $request, Peminjaman $peminjaman)
    {
        if (! $this->canView($request->user(), $peminjaman)) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if (! $peminjaman->surat_peminjaman) {
            return response()->json(['message' => 'Surat peminjaman tidak ada.'], 404);
        }

        if (! Storage::disk('public')->exists($peminjaman->surat_peminjaman)) {
            return response()->json(['message' => 'File tidak ditemukan di server.'], 404);
        }

        return response()->file(Storage::disk('public')->path($peminjaman->surat_peminjaman));
    }

    private function canView($user, Peminjaman $p): bool
    {
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }
        if ($user->isMahasiswa()) {
            return $p->user_id === $user->id;
        }

        return false;
    }
}
