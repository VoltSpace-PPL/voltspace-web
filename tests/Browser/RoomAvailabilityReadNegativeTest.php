<?php

namespace Tests\Browser;

use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class RoomAvailabilityReadNegativeTest extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        \Schema::disableForeignKeyConstraints();
        User::where('email', 'admin@voltspace.id')->delete();
        Ruangan::where('nama_ruangan', 'Server Room Alpha')->delete();
        \Schema::enableForeignKeyConstraints();

        User::factory()->create([
            'email' => 'admin@voltspace.id',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);

        $this->makeTestRuangan();
    }

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
     * PBI #36 – TC.RoomAvailabilityRead.Negative
     */
    public function test_user_cannot_read_unexisting_room_availability()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser->visit('/room-availability')
                ->waitForText('Room Availability', 10)
                ->waitUntilMissingText('Loading rooms...', 10)
                ->pause(1000)
                ->type('#room-search', 'Ruangan Tidak Ada')
                ->pause(1000)
                ->assertSee('No rooms found.');
        });
    }
}
