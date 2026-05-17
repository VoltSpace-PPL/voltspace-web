<?php

namespace Tests\Browser;

use App\Models\JadwalListrik;
use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class ScheduleCreate002Test extends DuskTestCase
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
     * PBI#9 — TC.Schedule.Create.002 (Negative)
     */
    public function test_tc_schedule_create_002()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser->visit('/schedule')
                ->waitForText('Electricity Schedule')
                ->click('button[onclick="openAddScheduleModal()"]')
                ->waitForText('Add New Schedule')
                ->click('#add-days-container button[data-day="monday"]')
                ->script("document.querySelector('input[name=\"start_time\"]').value = '08:00';");

            $browser->script("document.querySelector('input[name=\"end_time\"]').value = '17:00';");

            $browser->select('automation_action', 'on')
                ->click('#add-schedule-form button[type="submit"]')
                ->pause(500)
                ->assertSee('Select Room');
        });
    }
}
