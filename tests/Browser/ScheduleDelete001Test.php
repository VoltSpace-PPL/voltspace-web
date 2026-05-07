<?php

namespace Tests\Browser;

use App\Models\Ruangan;
use App\Models\User;
use App\Models\JadwalListrik;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ScheduleDelete001Test extends DuskTestCase
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
     * PBI#12 — TC.Schedule.Delete.001 (Positive)
     */
    public function test_tc_schedule_delete_001()
    {
        $jadwal = JadwalListrik::create([
            'ruangan_id' => 'R-001',
            'selected_days' => ['monday'],
            'start_time' => '11:00',
            'end_time' => '13:00',
            'automation_action' => 'on',
            'schedule_status' => 'active',
            'waktu_mulai' => '11:00',
            'waktu_selesai' => '13:00',
            'status_listrik' => 'nyala'
        ]);

        $this->browse(function (Browser $browser) use ($jadwal) {
            $this->loginAdmin($browser);
            
            $browser->visit('/schedule')
                    ->waitForText('11:00 - 13:00')
                    ->click("button[data-delete-id=\"{$jadwal->id}\"]")
                    ->waitForText('Delete Schedule?')
                    ->click('#confirm-delete-schedule-btn')
                    ->waitUntilMissing('#delete-schedule-modal:not(.hidden)')
                    ->pause(1000)
                    ->assertDontSee('11:00 - 13:00')
                    ->assertSee('No schedules found');
        });
    }
}
