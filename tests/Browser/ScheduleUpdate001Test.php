<?php

namespace Tests\Browser;

use App\Models\Ruangan;
use App\Models\User;
use App\Models\JadwalListrik;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ScheduleUpdate001Test extends DuskTestCase
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
     * PBI#11 — TC.Schedule.Update.001 (Positive)
     */
    public function test_tc_schedule_update_001()
    {
        $jadwal = JadwalListrik::create([
            'ruangan_id' => 'R-001',
            'selected_days' => ['monday'],
            'start_time' => '07:00',
            'end_time' => '15:00',
            'automation_action' => 'on',
            'schedule_status' => 'active',
            'waktu_mulai' => '07:00',
            'waktu_selesai' => '15:00',
            'status_listrik' => 'nyala'
        ]);

        $this->browse(function (Browser $browser) use ($jadwal) {
            $this->loginAdmin($browser);
            
            $browser->visit('/schedule')
                    ->waitForText('07:00 - 15:00')
                    ->click("button[data-edit-id=\"{$jadwal->id}\"]")
                    ->waitForText('Edit Schedule')
                    ->script("document.querySelector('input[name=\"edit_start_time\"]').value = '09:00';");
            
            $browser->script("document.querySelector('input[name=\"edit_end_time\"]').value = '16:00';");
            
            $browser->click('#edit-days-container button[data-day="monday"]') // toggle off
                    ->click('#edit-days-container button[data-day="tuesday"]') // toggle on
                    ->click('#edit-schedule-form button[type="submit"]')
                    ->waitUntilMissing('#edit-schedule-modal:not(.hidden)')
                    ->pause(1000)
                    ->assertSee('09:00 - 16:00')
                    ->assertSee('Tuesday')
                    ->assertDontSee('07:00 - 15:00');
        });
    }
}
