<?php

namespace Tests\Browser;

use App\Models\Ruangan;
use App\Models\User;
use App\Models\JadwalListrik;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ScheduleRead001Test extends DuskTestCase
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
     * PBI#10 — TC.Schedule.Read.001 (Positive)
     */
    public function test_tc_schedule_read_001()
    {
        JadwalListrik::create([
            'ruangan_id' => 'R-001',
            'selected_days' => ['monday', 'friday'],
            'start_time' => '10:00',
            'end_time' => '12:00',
            'automation_action' => 'on',
            'schedule_status' => 'active',
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '12:00',
            'status_listrik' => 'nyala'
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);
            
            $browser->visit('/schedule')
                    ->waitForText('Electricity Schedule')
                    ->waitForText('Server Room Alpha')
                    ->assertSee('Monday, Friday')
                    ->assertSee('10:00 - 12:00');
        });
    }
}
