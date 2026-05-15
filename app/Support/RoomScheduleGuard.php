<?php

namespace App\Support;

use App\Models\JadwalListrik;
use App\Models\Peminjaman;
use Carbon\Carbon;

final class RoomScheduleGuard
{
    public static function dateRangesOverlap(Carbon $aStart, Carbon $aEnd, Carbon $bStart, Carbon $bEnd): bool
    {
        return $aStart->lte($bEnd) && $bStart->lte($aEnd);
    }

    public static function clockRangesOverlap(string $t1Start, string $t1End, string $t2Start, string $t2End): bool
    {
        $a1 = Carbon::parse('2000-01-01 '.$t1Start);
        $a2 = Carbon::parse('2000-01-01 '.$t1End);
        $b1 = Carbon::parse('2000-01-01 '.$t2Start);
        $b2 = Carbon::parse('2000-01-01 '.$t2End);

        return $a1->lt($b2) && $b1->lt($a2);
    }

    /**
     * @param  array<int, string>  $ignoreStatusesPeminjaman  statuses to skip (e.g. ditolak, dibatalkan)
     */
    public static function peminjamanBlocks(
        string $ruanganId,
        Carbon $tanggalMulai,
        Carbon $tanggalSelesai,
        string $waktuMulai,
        string $waktuSelesai,
        array $ignoreStatusesPeminjaman = ['ditolak', 'dibatalkan'],
        ?int $exceptPeminjamanId = null,
    ): bool {
        $q = Peminjaman::query()
            ->where('ruangan_id', $ruanganId)
            ->whereNotIn('status', $ignoreStatusesPeminjaman);

        if ($exceptPeminjamanId) {
            $q->where('id', '!=', $exceptPeminjamanId);
        }

        foreach ($q->cursor() as $p) {
            $pStart = Carbon::parse($p->tanggal_mulai)->startOfDay();
            $pEnd = Carbon::parse($p->tanggal_selesai)->startOfDay();
            if (! self::dateRangesOverlap($tanggalMulai->copy()->startOfDay(), $tanggalSelesai->copy()->startOfDay(), $pStart, $pEnd)) {
                continue;
            }
            $wm = self::normalizeTime((string) $p->waktu_mulai);
            $ws = self::normalizeTime((string) $p->waktu_selesai);
            if (self::clockRangesOverlap($waktuMulai, $waktuSelesai, $wm, $ws)) {
                return true;
            }
        }

        return false;
    }

    public static function jadwalListrikBlocks(
        string $ruanganId,
        Carbon $tanggalMulai,
        Carbon $tanggalSelesai,
        string $waktuMulai,
        string $waktuSelesai,
    ): bool {
        $q = JadwalListrik::query()
            ->where('ruangan_id', $ruanganId)
            ->where('schedule_status', 'active');

        $peminjamanDays = [];
        for ($d = $tanggalMulai->copy()->startOfDay(); $d->lte($tanggalSelesai->copy()->startOfDay()); $d->addDay()) {
            $peminjamanDays[] = $d->copy();
        }

        foreach ($q->cursor() as $row) {
            $jlStart = $row->tanggal_mulai ? Carbon::parse($row->tanggal_mulai)->startOfDay() : null;
            $jlEnd = $row->tanggal_selesai ? Carbon::parse($row->tanggal_selesai)->startOfDay() : ($jlStart ? $jlStart->copy() : null);
            $daysArray = array_map('strtolower', (array) $row->selected_days);

            foreach ($peminjamanDays as $pDay) {
                $isWithinDate = true;
                if ($jlStart && $jlEnd) {
                    $isWithinDate = $pDay->between($jlStart, $jlEnd);
                }

                if ($isWithinDate) {
                    $dayOfWeek = strtolower($pDay->englishDayOfWeek);
                    // Jika selected_days kosong, berarti berlaku untuk semua hari di rentang tanggal tersebut
                    if (empty($daysArray) || in_array($dayOfWeek, $daysArray, true)) {
                        $wm = self::normalizeTime((string) $row->waktu_mulai);
                        $ws = self::normalizeTime((string) $row->waktu_selesai);
                        if (self::clockRangesOverlap($waktuMulai, $waktuSelesai, $wm, $ws)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    private static function normalizeTime(string $t): string
    {
        $t = trim($t);
        if (strlen($t) > 5) {
            return substr($t, 0, 5);
        }

        return $t;
    }
}
