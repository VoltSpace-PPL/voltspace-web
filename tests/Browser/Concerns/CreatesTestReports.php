<?php

namespace Tests\Browser\Concerns;

use App\Models\GeneratedEnergyReport;
use App\Models\MonitoringEnergi;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;

trait CreatesTestReports
{
    protected int $reportId = 0;

    protected function prepareReportDuskTest(bool $withStudent = false): void
    {
        \Schema::disableForeignKeyConstraints();
        \DB::table('generated_energy_reports')->truncate();
        MonitoringEnergi::query()->delete();
        Ruangan::where('nama_ruangan', 'Lab Energi Test')->delete();
        User::where('email', 'admin@voltspace.id')->delete();
        if ($withStudent) {
            User::where('email', 'student@voltspace.id')->delete();
        }
        \Schema::enableForeignKeyConstraints();

        User::factory()->create([
            'email' => 'admin@voltspace.id',
            'role' => 'admin',
            'password' => 'admin123',
        ]);

        if ($withStudent) {
            User::factory()->create([
                'email' => 'student@voltspace.id',
                'role' => 'mahasiswa',
                'password' => 'student123',
            ]);
        }

        $this->seedEnergyMonitoringData();
    }

    protected function seedEnergyMonitoringData(): Ruangan
    {
        $ruangan = Ruangan::query()->create([
            'id' => 'RM-RPT',
            'kode' => 'RM-RPT',
            'nama_ruangan' => 'Lab Energi Test',
            'kapasitas' => 30,
            'status' => 'digunakan',
        ]);

        MonitoringEnergi::query()->create([
            'ruangan_id' => $ruangan->id,
            'bulan' => 6,
            'tahun' => 2026,
            'konsumsi_kwh' => 42.5,
        ]);

        return $ruangan;
    }

    protected function generateReportForAdmin(int $bulan = 6, int $tahun = 2026): GeneratedEnergyReport
    {
        $admin = User::where('email', 'admin@voltspace.id')->firstOrFail();
        $token = $admin->createToken('dusk-report')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$token])
            ->postJson('/api/laporan-energi/generate', [
                'jenis_periode' => 'bulanan',
                'bulan' => $bulan,
                'tahun' => $tahun,
            ]);

        if (! $response->isSuccessful()) {
            throw new \RuntimeException('Failed to seed report: '.$response->getContent());
        }

        $report = GeneratedEnergyReport::query()->findOrFail($response->json('data.id'));
        $this->reportId = $report->id;

        return $report;
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

    protected function loginStudent(Browser $browser): void
    {
        $browser->visit('/login')
            ->waitFor('input[name="email"]', 10)
            ->type('email', 'student@voltspace.id')
            ->type('password', 'student123')
            ->press('Sign In')
            ->waitForLocation('/student/dashboard', 15)
            ->pause(500);
    }

    protected function visitReportsPage(Browser $browser): Browser
    {
        return $browser->visit('/reports')
            ->waitForText('Generate Electricity Report', 15)
            ->pause(1000);
    }

    protected function waitForReportsList(Browser $browser): Browser
    {
        return $browser->waitUntilMissingText('Loading reports...', 15);
    }

    protected function clickVsAlertButton(Browser $browser, string $text): void
    {
        $escaped = addslashes($text);
        $browser->script("
            Array.from(document.querySelectorAll('#vs-alert-actions button'))
                .find(function (btn) { return btn.textContent.trim() === '{$escaped}'; })
                .click();
        ");
    }

    protected function dismissVsAlert(Browser $browser): void
    {
        $browser->script("document.querySelector('#vs-alert-actions button').click();");
    }

    /**
     * @return array{status: int, message?: string}
     */
    protected function callReportApiAsBrowser(Browser $browser, string $method, string $path): array
    {
        $browser->script("
            window.__duskReportApiResult = null;
            fetch('{$path}', {
                method: '{$method}',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(function (response) {
                return response.json()
                    .catch(function () { return {}; })
                    .then(function (body) {
                        window.__duskReportApiResult = {
                            status: response.status,
                            message: body.message || null
                        };
                    });
            })
            .catch(function () {
                window.__duskReportApiResult = { status: 0, message: null };
            });
        ");

        $browser->waitUntil('window.__duskReportApiResult !== null', 15);

        /** @var array{status: int, message?: string} $result */
        $result = $browser->script('return window.__duskReportApiResult;')[0];

        return $result;
    }

    protected function cleanupReportStorage(): void
    {
        if (Storage::disk('local')->exists('reports')) {
            Storage::disk('local')->deleteDirectory('reports');
        }
    }
}
