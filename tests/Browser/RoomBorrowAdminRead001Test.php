<?php

namespace Tests\Browser;

use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

/**
 * PBI #27 – TC.RoomBorrow.AdminRead.01 (Positive)
 *
 * Memvalidasi admin dapat membaca daftar pengajuan peminjaman ruangan
 * dari semua mahasiswa di halaman /bookings untuk keperluan approval.
 */
class RoomBorrowAdminRead001Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();

        // Bersihkan data yang mungkin konflik dari test sebelumnya
        \Schema::disableForeignKeyConstraints();
        \DB::table('peminjaman')->truncate();
        User::where('email', 'student@voltspace.id')->delete();
        Ruangan::where('nama_ruangan', 'Server Room Alpha')->delete();
        \Schema::enableForeignKeyConstraints();

        // Buat mahasiswa
        $student = User::factory()->create([
            'name'     => 'Budi Santoso',
            'email'    => 'student@voltspace.id',
            'role'     => 'mahasiswa',
            'password' => bcrypt('student123'),
        ]);

        // Buat ruangan
        $room = $this->makeTestRuangan();
        $this->testRuanganId = $room->id;

        // Buat 1 data peminjaman pending dengan ID 1 agar tampil sebagai BK001
        // Karena tabel sudah di-truncate, auto-increment akan dimulai dari ID berikutnya.
        // Kita force insert dengan id=1 agar sesuai skenario BK001.
        \DB::table('peminjaman')->insert([
            'id'               => 1,
            'user_id'          => $student->id,
            'ruangan_id'       => $room->id,
            'tanggal_mulai'    => '2026-12-31',
            'tanggal_selesai'  => '2026-12-31',
            'waktu_mulai'      => '08:00:00',
            'waktu_selesai'    => '10:00:00',
            'tujuan'           => 'Rapat Organisasi',
            'status'           => 'pending',
            'surat_peminjaman' => 'dummy_surat.xlsx',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }

    /**
     * Login sebagai admin dan tunggu redirect ke /dashboard.
     */
    protected function loginAdmin(Browser $browser): void
    {
        $browser->visit('/login')
            ->waitFor('input[name="email"]', 10)
            ->type('email', 'admin@voltspace.id')
            ->type('password', 'admin123')
            ->press('Sign In')
            ->waitForLocation('/dashboard', 15)
            ->pause(500);
    }

    /**
     * TC.RoomBorrow.AdminRead.01 – Step 1–4
     *
     * Step 1 : Buka /bookings, tunggu judul dan hilangnya spinner loading.
     * Step 2 : Verifikasi ruangan, status Pending, dan ID booking BK001.
     * Step 3 : Verifikasi nama mahasiswa dan prefix NIM/email.
     * Step 4 : Verifikasi tanggal dan rentang waktu.
     */
    public function test_tc_room_borrow_admin_read_001(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            // Step 1 – Navigasi ke halaman Room Bookings Management
            $browser->visit('/bookings')
                ->waitForText('Room Bookings Management', 15)
                ->waitUntilMissingText('Loading bookings...');

            // Step 2 – Ruangan, status badge Pending, dan ID booking BK001 tampil
            $browser->assertSee('Server Room Alpha')
                ->assertSee('Pending')
                ->assertSee('BK001');

            // Step 3 – Nama mahasiswa dan prefix NIM/email tampil di kolom Student
            $browser->assertSee('Budi Santoso')
                ->assertSee('student');

            // Step 4 – Tanggal dan rentang waktu tampil
            $browser->assertSee('2026-12-31')
                ->assertSee('08:00 - 10:00 WIB');
        });
    }
}
