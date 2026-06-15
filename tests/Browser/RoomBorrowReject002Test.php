<?php

namespace Tests\Browser;

use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

/**
 * PBI #29 – TC.RoomBorrow.Reject.02 (Negative)
 *
 * Memvalidasi sistem menolak submit reject tanpa mengisi alasan;
 * alert "Reason Required" muncul dan pengajuan tetap pending.
 */
class RoomBorrowReject002Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected int $bookingId;

    protected function setUp(): void
    {
        parent::setUp();

        \Schema::disableForeignKeyConstraints();
        \DB::table('peminjaman')->truncate();

        // Bersihkan jadwal_listriks agar tidak ada konflik jadwal pada run berikutnya
        $existingRoomIds = \DB::table('ruangans')
            ->where('nama_ruangan', 'Server Room Alpha')
            ->pluck('id');
        if ($existingRoomIds->isNotEmpty()) {
            \DB::table('jadwal_listriks')->whereIn('ruangan_id', $existingRoomIds)->delete();
        }

        User::where('email', 'student@voltspace.id')->delete();
        Ruangan::where('nama_ruangan', 'Server Room Alpha')->delete();
        \Schema::enableForeignKeyConstraints();

        $student = User::factory()->create([
            'name'     => 'Budi Santoso',
            'email'    => 'student@voltspace.id',
            'role'     => 'mahasiswa',
            'password' => bcrypt('student123'),
        ]);

        $room = $this->makeTestRuangan();
        $this->testRuanganId = $room->id;

        $booking = Peminjaman::create([
            'user_id'          => $student->id,
            'ruangan_id'       => $room->id,
            'tanggal_mulai'    => '2026-12-31',
            'tanggal_selesai'  => '2026-12-31',
            'waktu_mulai'      => '08:00:00',
            'waktu_selesai'    => '10:00:00',
            'tujuan'           => 'Rapat Organisasi',
            'status'           => 'pending',
            'surat_peminjaman' => 'dummy_surat.xlsx',
        ]);

        $this->bookingId = $booking->id;
    }

    /**
     * TC.RoomBorrow.Reject.02 – Step 1–4
     *
     * Step 1 : Login admin, buka /bookings, buka modal Reject tanpa mengisi alasan.
     * Step 2 : Pastikan textarea kosong lalu klik tombol Reject Booking.
     * Step 3 : Alert peringatan "Reason Required" muncul dengan pesan validasi.
     * Step 4 : Tutup warning, verifikasi pengajuan tetap Pending di UI dan DB.
     */
    public function test_tc_room_borrow_reject_002(): void
    {
        $bookingId = $this->bookingId;

        $this->browse(function (Browser $browser) use ($bookingId) {
            // Step 1 – Login admin, buka halaman bookings, buka modal Reject
            $this->loginAdmin($browser);

            $browser->visit('/bookings')
                ->waitUntilMissingText('Loading bookings...')
                ->pause(1000);

            // Buka modal reject via JS
            $browser->script("openRejectModal({$bookingId});");
            $browser->waitFor('#reject-booking-modal', 10);

            // Step 2 – Pastikan textarea kosong (openRejectModal() memang selalu mengosongkan),
            //          lalu klik tombol Reject Booking tanpa mengisi alasan
            $browser->clear('#reject-reason-input');
            $browser->click('#confirm-reject-btn');

            // Step 3 – Alert peringatan Reason Required harus muncul
            $browser->waitForText('Reason Required', 10)
                ->assertSee('Please provide a reason for rejection.');

            // Tutup alert peringatan (reject modal masih terbuka di bawahnya)
            $browser->script("vsAlert.close();");
            $browser->pause(500);

            // Step 4 – Pengajuan tetap Pending di UI, tombol Reject masih tersedia
            // (modal reject masih terbuka; tabel di balik overlay masih punya data pending)
            $browser->assertSee('Pending')
                ->assertPresent('button[title="Reject"]');

            // Verifikasi status dan catatan_admin di database tidak berubah
            $this->assertEquals(
                'pending',
                Peminjaman::find($bookingId)?->status,
                'Status peminjaman harus tetap pending.'
            );
            $this->assertNull(
                Peminjaman::find($bookingId)?->catatan_admin,
                'catatan_admin harus tetap null karena reject belum berhasil.'
            );
        });
    }
}
