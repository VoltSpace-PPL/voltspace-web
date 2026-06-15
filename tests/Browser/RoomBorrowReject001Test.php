<?php

namespace Tests\Browser;

use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

/**
 * PBI #29 – TC.RoomBorrow.Reject.01 (Positive)
 *
 * Memvalidasi admin dapat reject pengajuan melalui tombol Reject di baris
 * tabel /bookings beserta pengisian alasan penolakan.
 */
class RoomBorrowReject001Test extends DuskTestCase
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
     * TC.RoomBorrow.Reject.01 – Step 1–7
     *
     * Step 1 : Login admin, buka /bookings, tunggu data selesai dimuat.
     * Step 2 : Verifikasi ruangan, badge Pending, dan tombol Reject tersedia.
     * Step 3 : Buka modal Reject via JS, tunggu modal dan judul tampil.
     * Step 4 : Isi alasan penolakan di textarea.
     * Step 5 : Klik Reject Booking, tunggu alert sukses Rejected, tutup alert.
     * Step 6 : Verifikasi badge berubah Rejected, Pending hilang, tombol Reject hilang.
     * Step 7 : Verifikasi status DB = ditolak dan catatan_admin terisi.
     */
    public function test_tc_room_borrow_reject_001(): void
    {
        $bookingId = $this->bookingId;

        $this->browse(function (Browser $browser) use ($bookingId) {
            // Step 1 – Login admin dan buka halaman bookings
            $this->loginAdmin($browser);

            $browser->visit('/bookings')
                ->waitForText('Room Bookings Management', 15)
                ->waitUntilMissingText('Loading bookings...')
                ->pause(1000);

            // Step 2 – Ruangan, badge Pending, dan tombol Reject tersedia di tabel
            // Catatan: kolom 'tujuan' tidak ditampilkan di tabel; verifikasi via nama ruangan.
            $browser->assertSee('Server Room Alpha')
                ->assertSee('Pending')
                ->assertPresent('button[title="Reject"]');

            // Step 3 – Buka modal Reject via JS, tunggu modal dan judul tampil
            $browser->script("openRejectModal({$bookingId});");
            $browser->waitFor('#reject-booking-modal', 10);
            $browser->waitForText('Reject Booking');

            // Step 4 – Isi alasan penolakan di textarea
            $browser->type('#reject-reason-input', 'Jadwal bentrok dengan kegiatan fakultas');

            // Step 5 – Klik tombol Reject Booking, tunggu alert sukses, tutup alert
            $browser->click('#confirm-reject-btn')
                ->waitForText('Rejected', 15);

            // Tutup alert sukses (script() tidak bisa di-chain, pisah statement)
            $browser->script("document.querySelector('#vs-alert-actions button').click();");

            // Step 6 – Verifikasi UI setelah tabel reload
            // pause(2000) memberi waktu loadBookings() menyelesaikan re-render
            $browser->pause(2000)
                ->assertSee('Rejected')
                ->assertDontSeeIn('#bookingsTableBody', 'Pending')
                ->assertMissing("button[onclick=\"openRejectModal({$bookingId})\"]");

            // Step 7 – Verifikasi status di database = ditolak
            $browser->waitUsing(15, 250, function () use ($bookingId) {
                return Peminjaman::find($bookingId)?->status === 'ditolak';
            });

            $this->assertEquals(
                'Jadwal bentrok dengan kegiatan fakultas',
                Peminjaman::find($bookingId)?->catatan_admin,
                'catatan_admin harus terisi dengan alasan penolakan.'
            );
        });
    }
}
