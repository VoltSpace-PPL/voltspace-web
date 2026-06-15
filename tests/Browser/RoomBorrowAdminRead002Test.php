<?php

namespace Tests\Browser;

use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

/**
 * PBI #27 – TC.RoomBorrow.AdminRead.02 (Negative)
 *
 * Memvalidasi guest tidak dapat membaca data pengajuan di /bookings;
 * pengguna tanpa token harus diarahkan kembali ke /login.
 */
class RoomBorrowAdminRead002Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan data peminjaman di database agar ada sesuatu yang bisa "bocor"
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

        Peminjaman::create([
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
    }

    /**
     * TC.RoomBorrow.AdminRead.02 – Step 1–3
     *
     * Step 1 : Kunjungi /login, kemudian hapus token dari localStorage
     *          agar simulasi pengguna guest (belum login).
     * Step 2 : Kunjungi /bookings dan tunggu 2 detik – sistem mendeteksi
     *          tidak ada token lalu mengarahkan ke /login.
     * Step 3 : Verifikasi URL berada di /login dan halaman
     *          Room Bookings Management tidak tampil.
     */
    public function test_tc_room_borrow_admin_read_002(): void
    {
        $this->browse(function (Browser $browser) {
            // Step 1 – Kunjungi /login lalu hapus token autentikasi dari localStorage
            $browser->visit('/login')
                ->script("localStorage.removeItem('token');");

            // Step 2 – Coba akses /bookings sebagai guest, tunggu redirect
            $browser->visit('/bookings')
                ->pause(2000);

            // Step 3 – Harus diarahkan ke /login, halaman manajemen tidak muncul
            $browser->assertPathIs('/login')
                ->assertDontSee('Room Bookings Management');
        });
    }
}
