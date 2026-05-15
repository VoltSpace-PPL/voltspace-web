<?php

namespace Tests\Feature;

use App\Models\EnergyAlertSetting;
use App\Models\MonitoringEnergi;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileAndEnergyAlertApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_read_excludes_energy_alert_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'energy_alert_high_usage_kwh' => 170.5,
            'energy_alert_peak_kw' => 12.25,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/profile')
            ->assertOk()
            ->assertJsonMissingPath('energy_alert_high_usage_kwh')
            ->assertJsonMissingPath('energy_alert_peak_kw')
            ->assertJsonPath('email', $user->email);
    }

    public function test_energy_alerts_returns_exceeded_and_almost_exceeded(): void
    {
        EnergyAlertSetting::query()->create([
            'high_usage_threshold_kwh' => 100,
            'peak_demand_limit_kw' => 10,
        ]);

        Ruangan::query()->create([
            'id' => 'RM-HIGH',
            'kode' => 'RM-HIGH',
            'nama_ruangan' => 'Ruang Tinggi',
            'kapasitas' => 30,
            'status' => 'digunakan',
        ]);

        Ruangan::query()->create([
            'id' => 'RM-WARN',
            'kode' => 'RM-WARN',
            'nama_ruangan' => 'Ruang Peringatan',
            'kapasitas' => 20,
            'status' => 'digunakan',
        ]);

        MonitoringEnergi::query()->create([
            'ruangan_id' => 'RM-HIGH',
            'bulan' => (int) now()->month,
            'tahun' => (int) now()->year,
            'konsumsi_kwh' => 120,
        ]);

        MonitoringEnergi::query()->create([
            'ruangan_id' => 'RM-WARN',
            'bulan' => (int) now()->month,
            'tahun' => (int) now()->year,
            'konsumsi_kwh' => 90,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/energy-alerts')
            ->assertOk();

        $statuses = collect($response->json('alerts'))->pluck('status')->all();

        $this->assertContains('exceeded', $statuses);
        $this->assertContains('almost_exceeded', $statuses);
    }
}
