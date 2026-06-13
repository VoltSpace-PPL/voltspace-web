<?php

namespace Tests\Browser;

use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class RoomBorrowCancel002Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        \Schema::disableForeignKeyConstraints();
        \DB::table('peminjaman')->truncate();
        User::where('email', 'student@voltspace.id')->delete();
        Ruangan::where('nama_ruangan', 'Server Room Alpha')->delete();
        \Schema::enableForeignKeyConstraints();

        $user = User::factory()->create([
            'email' => 'student@voltspace.id',
            'role' => 'mahasiswa',
            'password' => bcrypt('student123'),
        ]);

        $room = $this->makeTestRuangan();
        $this->testRuanganId = $room->id;

        Peminjaman::create([
            'user_id' => $user->id,
            'ruangan_id' => $room->id,
            'tanggal_mulai' => '2026-12-31',
            'tanggal_selesai' => '2026-12-31',
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'tujuan' => 'Rapat Organisasi',
            'status' => 'disetujui',
            'surat_peminjaman' => 'dummy_surat.xlsx',
        ]);
    }

    protected function loginStudent(Browser $browser): void
    {
        $browser->visit('/login')
            ->waitFor('input[name="email"]', 10)
            ->type('email', 'student@voltspace.id')
            ->type('password', 'student123')
            ->press('Sign In')
            ->waitForLocation('/student/dashboard', 15)
            ->pause(500);
    }

    /**
     * PBI #32 TC.RoomBorrow.Cancel.002 (Negative)
     */
    public function test_tc_room_borrow_cancel_002()
    {
        $this->browse(function (Browser $browser) {
            $this->loginStudent($browser);

            $browser->visit('/student/bookings')
                ->waitForText('My Bookings')
                ->waitUntilMissingText('Loading bookings...')
                ->pause(1000)
                ->assertSee('Rapat Organisasi')
                ->assertSee('Approved')
                ->assertDontSee('Cancel Booking');
        });
    }
}
