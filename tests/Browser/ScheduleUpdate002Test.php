<?php

namespace Tests\Browser;

use App\Models\JadwalListrik;
use App\Models\Ruangan;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class ScheduleUpdate002Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected string $testRuanganId = '';

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

        $room = $this->makeTestRuangan();
        $this->testRuanganId = $room->id;
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
     * PBI#11 — TC.Schedule.Update.002 (Negative)
     */
    public function test_tc_schedule_update_002()
    {
        $jadwal = JadwalListrik::create([
            'ruangan_id' => $this->testRuanganId,
            'selected_days' => ['monday'],
            'start_time' => '07:00',
            'end_time' => '15:00',
            'automation_action' => 'on',
            'schedule_status' => 'active',
            'waktu_mulai' => '07:00',
            'waktu_selesai' => '15:00',
            'status_listrik' => 'nyala',
        ]);

        $this->browse(function (Browser $browser) use ($jadwal) {
            $this->loginAdmin($browser);

            $browser->visit('/schedule')
                ->waitForText('07:00 - 15:00')
                ->click("button[data-edit-id=\"{$jadwal->id}\"]")
                ->waitForText('Edit Schedule')
                ->script("document.querySelector('input[name=\"edit_start_time\"]').value = '';");

            $browser->click('#edit-schedule-form button[type="submit"]')
                ->pause(1000)
                ->assertPresent('#edit-schedule-modal:not(.hidden)');
        });
    }
}
