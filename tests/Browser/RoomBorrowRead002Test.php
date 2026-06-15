<?php

namespace Tests\Browser;

use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class RoomBorrowRead002Test extends DuskTestCase
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

        User::factory()->create([
            'email' => 'student@voltspace.id',
            'role' => 'mahasiswa',
            'password' => bcrypt('student123'),
        ]);

        $room = $this->makeTestRuangan();
        $this->testRuanganId = $room->id;
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
     * PBI #31 TC.RoomBorrow.Read.002 (Negative)
     */
    public function test_tc_room_borrow_read_002()
    {
        $this->browse(function (Browser $browser) {
            $this->loginStudent($browser);

            $browser->visit('/student/bookings')
                ->waitForText('My Bookings')
                ->waitUntilMissingText('Loading bookings...')
                ->pause(1000)
                ->type('#booking-search', 'Dummy Data Tidak Ada')
                ->pause(1000)
                ->assertDontSee('Rapat Organisasi')
                ->assertSee('No bookings found.');
        });
    }
}
