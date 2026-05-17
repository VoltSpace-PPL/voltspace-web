<?php

namespace Tests\Browser;

use App\Models\JadwalListrik;
use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class ScheduleRead002Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();

        \Schema::disableForeignKeyConstraints();
        JadwalListrik::truncate();
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

    private function loginAdmin(Browser $browser)
    {
        $browser->visit('/login')
            ->type('email', 'admin@voltspace.id')
            ->type('password', 'admin123')
            ->press('Sign In')
            ->waitForLocation('/rooms')
            ->pause(500);
    }

    /**
     * PBI#10 — TC.Schedule.Read.002 (Negative)
     */
    public function test_tc_schedule_read_002()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser->visit('/schedule')
                ->waitForText('Electricity Schedule')
                ->assertSee('No schedules found');
        });
    }
}
