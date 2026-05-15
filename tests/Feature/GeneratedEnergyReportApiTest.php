<?php

namespace Tests\Feature;

use App\Models\GeneratedEnergyReport;
use App\Models\MonitoringEnergi;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GeneratedEnergyReportApiTest extends TestCase
{
    use RefreshDatabase;

    private function adminToken(): string
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        return $admin->createToken('test')->plainTextToken;
    }

    private function authHeaders(string $token): array
    {
        return ['Authorization' => 'Bearer '.$token];
    }

    private function seedRoomWithEnergy(): Ruangan
    {
        $ruangan = Ruangan::query()->create([
            'id' => 'RM-TEST',
            'kode' => 'RM-TEST',
            'nama_ruangan' => 'Lab Komputer',
            'kapasitas' => 30,
            'status' => 'digunakan',
        ]);

        MonitoringEnergi::query()->create([
            'ruangan_id' => $ruangan->id,
            'bulan' => 5,
            'tahun' => 2026,
            'konsumsi_kwh' => 42.5,
        ]);

        return $ruangan;
    }

    public function test_generate_bulanan_includes_all_rooms_and_preview_detail(): void
    {
        Storage::fake('local');
        $this->seedRoomWithEnergy();

        Ruangan::query()->create([
            'id' => 'RM-EMPTY',
            'kode' => 'RM-EMPTY',
            'nama_ruangan' => 'Ruang Kosong',
            'kapasitas' => 10,
            'status' => 'tersedia',
        ]);

        $token = $this->adminToken();

        $response = $this->withHeaders($this->authHeaders($token))
            ->postJson('/api/laporan-energi/generate', [
                'jenis_periode' => 'bulanan',
                'bulan' => 5,
                'tahun' => 2026,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.jenis_periode', 'bulanan')
            ->assertJsonPath('data.total_kwh_ringkasan', '42.500')
            ->assertJsonPath('data.jumlah_ruangan', 2);

        $reportId = $response->json('data.id');

        $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/laporan-energi/'.$reportId.'/preview')
            ->assertOk()
            ->assertJsonPath('data.jumlah_ruangan', 2)
            ->assertJsonFragment([
                'ruangan_id' => 'RM-TEST',
                'nama_ruangan' => 'Lab Komputer',
                'total_kwh' => 42.5,
            ])
            ->assertJsonFragment([
                'ruangan_id' => 'RM-EMPTY',
                'total_kwh' => 0,
            ]);

        $report = GeneratedEnergyReport::query()->findOrFail($reportId);
        Storage::disk('local')->assertExists($report->path);
    }

    public function test_generate_tahunan_aggregates_monthly_values(): void
    {
        Storage::fake('local');
        $ruangan = $this->seedRoomWithEnergy();

        MonitoringEnergi::query()->create([
            'ruangan_id' => $ruangan->id,
            'bulan' => 6,
            'tahun' => 2026,
            'konsumsi_kwh' => 7.5,
        ]);

        $token = $this->adminToken();

        $response = $this->withHeaders($this->authHeaders($token))
            ->postJson('/api/laporan-energi/generate', [
                'jenis_periode' => 'tahunan',
                'tahun' => 2026,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.jenis_periode', 'tahunan')
            ->assertJsonPath('data.bulan', null)
            ->assertJsonPath('data.total_kwh_ringkasan', '50.000');
    }

    public function test_list_download_and_delete_report(): void
    {
        Storage::fake('local');
        $this->seedRoomWithEnergy();
        $token = $this->adminToken();

        $create = $this->withHeaders($this->authHeaders($token))
            ->postJson('/api/laporan-energi/generate', [
                'jenis_periode' => 'bulanan',
                'bulan' => 5,
                'tahun' => 2026,
            ])
            ->assertCreated();

        $reportId = $create->json('data.id');

        $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/laporan-energi')
            ->assertOk()
            ->assertJsonPath('data.0.id', $reportId)
            ->assertJsonMissingPath('data.0.path');

        $this->withHeaders($this->authHeaders($token))
            ->get('/api/laporan-energi/'.$reportId.'/download')
            ->assertOk();

        $report = GeneratedEnergyReport::query()->findOrFail($reportId);
        $path = $report->path;

        $this->withHeaders($this->authHeaders($token))
            ->deleteJson('/api/laporan-energi/'.$reportId)
            ->assertOk()
            ->assertJsonPath('message', 'Laporan berhasil dihapus.');

        $this->assertDatabaseMissing('generated_energy_reports', ['id' => $reportId]);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_bulanan_requires_bulan_field(): void
    {
        $token = $this->adminToken();

        $this->withHeaders($this->authHeaders($token))
            ->postJson('/api/laporan-energi/generate', [
                'jenis_periode' => 'bulanan',
                'tahun' => 2026,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['bulan']);
    }

    public function test_mahasiswa_cannot_generate_report(): void
    {
        $user = User::factory()->create(['role' => 'mahasiswa']);
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeaders($this->authHeaders($token))
            ->postJson('/api/laporan-energi/generate', [
                'jenis_periode' => 'bulanan',
                'bulan' => 5,
                'tahun' => 2026,
            ])
            ->assertForbidden();
    }
}
