<?php

namespace Tests\Browser;

use App\Models\EnergyAlertSetting;
use App\Models\MonitoringEnergi;
use App\Models\Ruangan;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class EnergyAlertsReadPositiveTest extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        \Schema::disableForeignKeyConstraints();
        \DB::table('monitoring_energis')->truncate();
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

        // Ensure global threshold is set
        $setting = EnergyAlertSetting::firstOrCreate(['id' => 1]);
        $setting->update([
            'high_usage_threshold_kwh' => 100,
        ]);

        // Create monitoring data that exceeds the threshold to trigger critical alert
        MonitoringEnergi::create([
            'ruangan_id' => $room->id,
            'tahun' => Carbon::now()->year,
            'bulan' => Carbon::now()->month,
            'konsumsi_kwh' => 120,
        ]);
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
     * PBI #37 – TC.EnergyAlertsRead.Positive
     */
    public function test_admin_can_read_energy_alerts_when_critical()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser->visit('/alerts')
                ->waitForText('Server Room Alpha', 15)
                ->assertSee('Server Room Alpha');
        });
    }
}
