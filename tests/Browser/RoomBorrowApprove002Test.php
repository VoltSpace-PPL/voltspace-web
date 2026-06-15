<?php

namespace Tests\Browser;

use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

/**
 * PBI #28 – TC.RoomBorrow.Approve.02 (Negative)
 *
 * Memvalidasi pengajuan yang sudah disetujui tidak dapat di-approve ulang;
 * tombol Approve tidak ditampilkan dan API menolak permintaan approve paksa.
 */
class RoomBorrowApprove002Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected int $bookingId;

    protected function setUp(): void
    {
        parent::setUp();

        \Schema::disableForeignKeyConstraints();
        \DB::table('peminjaman')->truncate();
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

        $admin = User::where('email', 'admin@voltspace.id')->first();

        $booking = Peminjaman::create([
            'user_id'          => $student->id,
            'ruangan_id'       => $room->id,
            'tanggal_mulai'    => '2026-12-31',
            'tanggal_selesai'  => '2026-12-31',
            'waktu_mulai'      => '08:00:00',
            'waktu_selesai'    => '10:00:00',
            'tujuan'           => 'Rapat Organisasi',
            'status'           => 'disetujui',
            'surat_peminjaman' => 'dummy_surat.xlsx',
            'reviewed_at'      => now(),
            'reviewed_by'      => $admin?->id,
        ]);

        $this->bookingId = $booking->id;
    }

    /**
     * TC.RoomBorrow.Approve.02 – Step 1–4
     *
     * Step 1 : Buka /bookings, tunggu data selesai dimuat.
     * Step 2 : Verifikasi badge Approved tampil dan tombol Approve tidak ada.
     * Step 3 : Paksa panggil approveBooking() via JS, tunggu respons API.
     * Step 4 : Alert Failed muncul dengan pesan penolakan; status DB tetap disetujui.
     */
    public function test_tc_room_borrow_approve_002(): void
    {
        $bookingId = $this->bookingId;

        $this->browse(function (Browser $browser) use ($bookingId) {
            // Step 1 – Login admin dan buka halaman bookings
            $this->loginAdmin($browser);

            $browser->visit('/bookings')
                ->waitUntilMissingText('Loading bookings...');

            // Step 2 – Badge Approved tampil, tombol Approve tidak tersedia untuk baris ini
            $browser->assertSee('Approved')
                ->assertMissing("button[onclick=\"approveBooking({$bookingId})\"]");

            // Step 3 – Paksa panggil approveBooking via JS (simulasi akses tidak sah).
            // approveBooking() menampilkan dialog konfirmasi dulu sebelum memanggil API,
            // sehingga kita harus klik tombol konfirmasi agar API ter-hit dan return 422.
            $browser->script("approveBooking({$bookingId});");
            $browser->waitForText('Approve Booking', 10);
            // Klik tombol konfirmasi (last-child) agar API /approve dipanggil
            $browser->script("document.querySelector('#vs-alert-actions button:last-child').click();");
            $browser->pause(2000);

            // Step 4 – Alert Failed muncul karena status bukan pending
            $browser->assertSee('Failed')
                ->assertSee('Hanya pengajuan pending yang dapat disetujui.');

            // Verifikasi status di database tidak berubah
            $this->assertEquals(
                'disetujui',
                Peminjaman::find($bookingId)?->status,
                'Status peminjaman di database harus tetap disetujui.'
            );
        });
    }
}
