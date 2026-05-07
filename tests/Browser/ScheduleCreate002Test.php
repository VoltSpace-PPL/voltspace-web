<?php

namespace Tests\Browser;

use App\Models\Ruangan;
use App\Models\User;
use App\Models\JadwalListrik;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ScheduleCreate002Test extends DuskTestCase
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
     * PBI#9 — TC.Schedule.Create.002 (Negative)
     */
    public function test_tc_schedule_create_002()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);
            
            $browser->visit('/schedule')
                    ->waitForText('Electricity Schedule')
                    ->click("button[onclick=\"openAddScheduleModal()\"]")
                    ->waitForText('Add New Schedule')
                    // Biarkan dropdown ruangan_id kosong
                    ->click('#add-days-container button[data-day="tuesday"]')
                    ->script("document.querySelector('input[name=\"start_time\"]').value = '09:00';");
            
            $browser->script("document.querySelector('input[name=\"end_time\"]').value = '18:00';");
            
            $browser->click('#add-schedule-form button[type="submit"]')
                    ->pause(1000)
                    ->assertPresent('#add-schedule-modal:not(.hidden)');
        });
    }
}
