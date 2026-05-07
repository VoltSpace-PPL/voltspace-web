<?php

namespace Tests\Browser;

use App\Models\Ruangan;
use App\Models\User;
use App\Models\JadwalListrik;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ScheduleCreate001Test extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        \Schema::disableForeignKeyConstraints();
        \App\Models\JadwalListrik::truncate();
        \App\Models\User::where('email', 'admin@voltspace.id')->delete();
        \App\Models\Ruangan::where('id', 'R-001')->delete();
        \Schema::enableForeignKeyConstraints();
        
        User::factory()->create([
            'email' => 'admin@voltspace.id',
            'role' => 'admin',
            'password' => bcrypt('admin123')
        ]);
        
        Ruangan::create([
            'id' => 'R-001',
            'nama_ruangan' => 'Server Room Alpha',
            'lokasi' => 'Lantai 1',
            'kapasitas' => 10,
            'status' => 'tersedia'
        ]);
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
     * PBI#9 — TC.Schedule.Create.001 (Positive)
     */
    public function test_tc_schedule_create_001()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);
            
            $browser->visit('/schedule')
                    ->waitForText('Electricity Schedule')
                    ->click("button[onclick=\"openAddScheduleModal()\"]")
                    ->waitForText('Add New Schedule')
                    ->waitFor('select[name="ruangan_id"] option[value="R-001"]')
                    ->script("document.querySelector('select[name=\"ruangan_id\"]').value = 'R-001';");

            $browser->click('#add-days-container button[data-day="monday"]')
                    ->click('#add-days-container button[data-day="wednesday"]')
                    ->script("document.querySelector('input[name=\"start_time\"]').value = '08:00';");
            
            $browser->script("document.querySelector('input[name=\"end_time\"]').value = '17:00';");
            
            $browser->select('automation_action', 'on')
                    ->click('#add-schedule-form button[type="submit"]')
                    ->waitUntilMissing('#add-schedule-modal:not(.hidden)')
                    ->pause(1000)
                    ->assertSee('Server Room Alpha')
                    ->assertSee('08:00')
                    ->assertSee('17:00');
        });
    }
}
