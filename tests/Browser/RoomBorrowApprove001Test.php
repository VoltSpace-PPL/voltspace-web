<?php

namespace Tests\Browser;

use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

/**
 * PBI #28 – TC.RoomBorrow.Approve.01 (Positive)
 *
 * Memvalidasi admin dapat approve pengajuan peminjaman melalui
 * tombol Approve di baris tabel /bookings.
 */
class RoomBorrowApprove001Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected int $bookingId;

    protected function setUp(): void
    {
        parent::setUp();

        \Schema::disableForeignKeyConstraints();
        \DB::table('peminjaman')->truncate();

        // Bersihkan jadwal_listriks dari run sebelumnya yang dicreate saat approve berhasil.
        // Tanpa ini, RoomScheduleGuard::jadwalListrikBlocks() akan menemukan konflik
        // dan approve API akan return 422 pada run berikutnya.
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
     * TC.RoomBorrow.Approve.01 – Step 1–6
     *
     * Step 1 : Login admin, buka /bookings, tunggu data selesai dimuat.
     * Step 2 : Verifikasi pengajuan Rapat Organisasi, badge Pending, dan tombol Approve tersedia.
     * Step 3 : Panggil approveBooking() via JS, tunggu dialog konfirmasi muncul.
     * Step 4 : Klik tombol konfirmasi, tunggu alert sukses Approved!.
     * Step 5 : Tutup alert sukses, verifikasi badge berubah Approved, Pending hilang, tombol Approve hilang.
     * Step 6 : Verifikasi status di database berubah menjadi disetujui.
     */
    public function test_tc_room_borrow_approve_001(): void
    {
        $bookingId = $this->bookingId;

        $this->browse(function (Browser $browser) use ($bookingId) {
            // Step 1 – Login admin dan buka halaman bookings
            $this->loginAdmin($browser);

            $browser->visit('/bookings')
                ->waitForText('Room Bookings Management', 15)
                ->waitUntilMissingText('Loading bookings...')
                ->pause(1000);

            // Step 2 – Pengajuan, badge Pending, dan tombol Approve tersedia
            // Catatan: kolom 'tujuan' tidak ditampilkan di tabel (hanya di modal detail),
            // sehingga diverifikasi via nama ruangan dan badge status.
            $browser->assertSee('Server Room Alpha')
                ->assertSee('Pending')
                ->assertPresent('button[title="Approve"]');

            // Step 3 – Panggil approveBooking via JS, tunggu dialog konfirmasi
            // script() mengembalikan array, harus dipisah dari chain browser
            $browser->script("approveBooking({$bookingId});");
            $browser->waitForText('Approve Booking', 10);

            // Step 4 – Konfirmasi approve, tunggu alert sukses
            $browser->script("document.querySelector('#vs-alert-actions button:last-child').click();");
            $browser->waitForText('Approved!', 15);

            // Step 5 – Tutup alert sukses, verifikasi status berubah
            // Perlu pause lebih lama agar tabel sempat reload via API setelah approval
            $browser->script("document.querySelector('#vs-alert-actions button').click();");
            $browser->pause(2000)
                ->assertSee('Approved')
                // assertDontSee('Pending') tidak bisa dipakai karena dropdown filter
                // juga mengandung teks "Pending" – scope ke tbody agar spesifik
                ->assertDontSeeIn('#bookingsTableBody', 'Pending')
                ->assertMissing("button[onclick=\"approveBooking({$bookingId})\"]");

            // Step 6 – Verifikasi status di database menjadi disetujui
            $browser->waitUsing(15, 250, function () use ($bookingId) {
                return Peminjaman::find($bookingId)?->status === 'disetujui';
            });
        });
    }
}
