<?php

namespace App\Services;

use App\Models\MonitoringEnergi;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

final class EnergyReportService
{
    /**
     * @return array{
     *     jenis_periode: string,
     *     tahun: int,
     *     bulan: int|null,
     *     ruangan: list<array{ruangan_id: string, nama_ruangan: string, total_kwh: float}>,
     *     total_kwh: float,
     *     jumlah_ruangan: int
     * }
     */
    public function aggregate(string $jenisPeriode, int $tahun, ?int $bulan = null): array
    {
        $ruanganList = Ruangan::query()->orderBy('id')->get();

        if ($jenisPeriode === 'tahunan') {
            return $this->aggregateTahunan($ruanganList, $tahun);
        }

        return $this->aggregateBulanan($ruanganList, $tahun, (int) $bulan);
    }

    public function buildSpreadsheet(string $title, array $aggregate): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Energi');

        $periodeLabel = $this->periodeLabel(
            $aggregate['jenis_periode'],
            (int) $aggregate['tahun'],
            $aggregate['bulan'] ?? null,
        );

        $sheet->setCellValue('A1', $title);
        $sheet->setCellValue('A2', 'Periode: '.$periodeLabel);
        $sheet->setCellValue('A3', 'Digenerate: '.now()->format('d/m/Y H:i'));

        $headerRow = 5;
        $sheet->fromArray(['No', 'Nama Ruangan', 'Total KWh Ruangan'], null, 'A'.$headerRow, true);

        $row = $headerRow + 1;
        $no = 1;
        foreach ($aggregate['ruangan'] as $item) {
            $sheet->fromArray([
                $no++,
                $item['nama_ruangan'],
                round((float) $item['total_kwh'], 3),
            ], null, 'A'.$row, true);
            $row++;
        }

        $sheet->fromArray([
            '',
            'Total Keseluruhan KWH',
            round((float) $aggregate['total_kwh'], 3),
        ], null, 'A'.$row, true);

        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A'.$headerRow.':C'.$headerRow)->getFont()->setBold(true);
        $sheet->getStyle('A'.$headerRow.':C'.$headerRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');
        $sheet->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach (['A', 'B', 'C'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    public function periodeLabel(string $jenisPeriode, int $tahun, ?int $bulan = null): string
    {
        if ($jenisPeriode === 'tahunan') {
            return 'Tahunan '.$tahun;
        }

        $monthName = Carbon::create($tahun, (int) $bulan, 1)->translatedFormat('F');

        return 'Bulanan '.$monthName.' '.$tahun;
    }

    /**
     * @param  Collection<int, Ruangan>  $ruanganList
     */
    private function aggregateBulanan(Collection $ruanganList, int $tahun, int $bulan): array
    {
        $perRoom = MonitoringEnergi::query()
            ->select('ruangan_id', DB::raw('SUM(konsumsi_kwh) as total_kwh'))
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->groupBy('ruangan_id')
            ->pluck('total_kwh', 'ruangan_id');

        return $this->buildAggregatePayload($ruanganList, 'bulanan', $tahun, $bulan, $perRoom);
    }

    /**
     * @param  Collection<int, Ruangan>  $ruanganList
     */
    private function aggregateTahunan(Collection $ruanganList, int $tahun): array
    {
        $perRoom = MonitoringEnergi::query()
            ->select('ruangan_id', DB::raw('SUM(konsumsi_kwh) as total_kwh'))
            ->where('tahun', $tahun)
            ->groupBy('ruangan_id')
            ->pluck('total_kwh', 'ruangan_id');

        return $this->buildAggregatePayload($ruanganList, 'tahunan', $tahun, null, $perRoom);
    }

    /**
     * @param  Collection<int, Ruangan>  $ruanganList
     * @param  Collection<string, mixed>  $perRoom
     */
    private function buildAggregatePayload(
        Collection $ruanganList,
        string $jenisPeriode,
        int $tahun,
        ?int $bulan,
        Collection $perRoom,
    ): array {
        $ruangan = [];
        $total = 0.0;

        foreach ($ruanganList as $ruanganModel) {
            $kwh = round((float) ($perRoom[$ruanganModel->id] ?? 0), 3);
            $total += $kwh;
            $ruangan[] = [
                'ruangan_id' => $ruanganModel->id,
                'nama_ruangan' => $ruanganModel->nama_ruangan,
                'total_kwh' => $kwh,
            ];
        }

        return [
            'jenis_periode' => $jenisPeriode,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'ruangan' => $ruangan,
            'total_kwh' => round($total, 3),
            'jumlah_ruangan' => count($ruangan),
        ];
    }
}
