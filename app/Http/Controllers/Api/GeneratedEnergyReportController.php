<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeneratedEnergyReport;
use App\Services\EnergyReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GeneratedEnergyReportController extends Controller
{
    public function __construct(
        private readonly EnergyReportService $energyReportService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        if (! $request->user()->isStaffAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $paginator = GeneratedEnergyReport::query()
            ->with('author:id,name')
            ->orderByDesc('id')
            ->paginate((int) $request->integer('per_page', 20));

        $paginator->getCollection()->transform(fn (GeneratedEnergyReport $report) => $this->formatListItem($report));

        return response()->json($paginator);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->user()->isStaffAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $data = $request->validate([
            'jenis_periode' => ['required', 'string', Rule::in(['bulanan', 'tahunan'])],
            'bulan' => ['required_if:jenis_periode,bulanan', 'nullable', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $jenisPeriode = $data['jenis_periode'];
        $year = (int) $data['tahun'];
        $month = $jenisPeriode === 'bulanan' ? (int) $data['bulan'] : null;

        $aggregate = $this->energyReportService->aggregate($jenisPeriode, $year, $month);

        $title = $data['title'] ?? $this->defaultTitle($jenisPeriode, $year, $month);
        $dir = 'reports';
        Storage::disk('local')->makeDirectory($dir);

        $filename = sprintf(
            'laporan_energi_%s_%d%s_%s.xlsx',
            $jenisPeriode,
            $year,
            $month !== null ? '_'.str_pad((string) $month, 2, '0', STR_PAD_LEFT) : '',
            uniqid('', true)
        );
        $path = $dir.'/'.$filename;
        $full = Storage::disk('local')->path($path);

        $spreadsheet = $this->energyReportService->buildSpreadsheet($title, $aggregate);
        $writer = new Xlsx($spreadsheet);
        $writer->save($full);

        $meta = array_merge($aggregate, [
            'title' => $title,
            'periode_label' => $this->energyReportService->periodeLabel($jenisPeriode, $year, $month),
            'generated_at' => now()->toIso8601String(),
        ]);

        $report = GeneratedEnergyReport::create([
            'created_by' => $request->user()->id,
            'title' => $title,
            'jenis_periode' => $jenisPeriode,
            'bulan' => $month,
            'tahun' => $year,
            'total_kwh_ringkasan' => $aggregate['total_kwh'],
            'meta' => $meta,
            'disk' => 'local',
            'path' => $path,
            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'size_bytes' => filesize($full) ?: 0,
        ]);

        $report->load('author:id,name');

        return response()->json([
            'message' => 'Laporan berhasil digenerate.',
            'data' => $this->formatListItem($report),
        ], 201);
    }

    public function preview(Request $request, GeneratedEnergyReport $report): JsonResponse
    {
        if (! $request->user()->isStaffAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $meta = $report->meta ?? [];
        $ruangan = $meta['ruangan'] ?? null;

        if (! is_array($ruangan) || $ruangan === []) {
            $ruangan = $this->energyReportService->aggregate(
                $report->jenis_periode ?? 'bulanan',
                (int) $report->tahun,
                $report->bulan !== null ? (int) $report->bulan : null,
            )['ruangan'];
        }

        return response()->json([
            'data' => [
                'id' => $report->id,
                'title' => $report->title,
                'jenis_periode' => $report->jenis_periode ?? 'bulanan',
                'bulan' => $report->bulan,
                'tahun' => $report->tahun,
                'periode_label' => $meta['periode_label'] ?? $this->energyReportService->periodeLabel(
                    $report->jenis_periode ?? 'bulanan',
                    (int) $report->tahun,
                    $report->bulan !== null ? (int) $report->bulan : null,
                ),
                'total_kwh_ringkasan' => $report->total_kwh_ringkasan,
                'jumlah_ruangan' => count($ruangan),
                'ruangan' => $ruangan,
                'generated_at' => $meta['generated_at'] ?? $report->created_at?->toIso8601String(),
                'created_at' => $report->created_at,
            ],
        ]);
    }

    public function download(Request $request, GeneratedEnergyReport $report): JsonResponse|StreamedResponse
    {
        if (! $request->user()->isStaffAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if (! Storage::disk($report->disk)->exists($report->path)) {
            return response()->json(['message' => 'File tidak ditemukan.'], 404);
        }

        $downloadName = $this->downloadFilename($report);

        return Storage::disk($report->disk)->download($report->path, $downloadName, [
            'Content-Type' => $report->mime,
        ]);
    }

    public function destroy(Request $request, GeneratedEnergyReport $report): JsonResponse
    {
        if (! $request->user()->isStaffAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if (Storage::disk($report->disk)->exists($report->path)) {
            Storage::disk($report->disk)->delete($report->path);
        }

        $report->delete();

        return response()->json(['message' => 'Laporan berhasil dihapus.']);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatListItem(GeneratedEnergyReport $report): array
    {
        $meta = $report->meta ?? [];
        $jenisPeriode = $report->jenis_periode ?? 'bulanan';

        return [
            'id' => $report->id,
            'title' => $report->title,
            'jenis_periode' => $jenisPeriode,
            'bulan' => $report->bulan,
            'tahun' => $report->tahun,
            'periode_label' => $meta['periode_label'] ?? $this->energyReportService->periodeLabel(
                $jenisPeriode,
                (int) $report->tahun,
                $report->bulan !== null ? (int) $report->bulan : null,
            ),
            'total_kwh_ringkasan' => $report->total_kwh_ringkasan,
            'jumlah_ruangan' => $meta['jumlah_ruangan'] ?? count($meta['ruangan'] ?? []),
            'created_by' => $report->created_by,
            'created_by_name' => $report->author?->name,
            'created_at' => $report->created_at,
            'updated_at' => $report->updated_at,
        ];
    }

    private function defaultTitle(string $jenisPeriode, int $year, ?int $month): string
    {
        if ($jenisPeriode === 'tahunan') {
            return 'Laporan Energi Tahunan '.$year;
        }

        $monthName = Carbon::create($year, (int) $month, 1)->translatedFormat('F');

        return 'Laporan Energi Bulanan '.$monthName.' '.$year;
    }

    private function downloadFilename(GeneratedEnergyReport $report): string
    {
        $jenis = $report->jenis_periode ?? 'bulanan';

        if ($jenis === 'tahunan') {
            return 'laporan_energi_tahunan_'.$report->tahun.'.xlsx';
        }

        return 'laporan_energi_bulanan_'.$report->tahun.'_'.str_pad((string) $report->bulan, 2, '0', STR_PAD_LEFT).'.xlsx';
    }
}
