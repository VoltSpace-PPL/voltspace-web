<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\MonitoringEnergi;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DatabaseStructureOptimizationTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::disableForeignKeyConstraints();

        MonitoringEnergi::whereIn('ruangan_id', ['RM-PBI47-A', 'RM-PBI47-B'])->delete();
        Device::whereIn('ruangan_id', ['RM-PBI47-A', 'RM-PBI47-B'])->delete();
        Ruangan::whereIn('id', ['RM-PBI47-A', 'RM-PBI47-B'])->delete();
        User::where('email', 'admin@voltspace.id')->delete();

        Schema::enableForeignKeyConstraints();

        User::factory()->create([
            'name' => 'Admin DB Test',
            'email' => 'admin@voltspace.id',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);

        $this->insertRoom('RM-PBI47-A', 'Database Room A', 'digunakan');
        $this->insertRoom('RM-PBI47-B', 'Database Room B', 'tersedia');

        Device::create([
            'name' => 'Optimized Meter A',
            'type' => 'Energy Meter',
            'ip_address' => '127.0.0.1',
            'ruangan_id' => 'RM-PBI47-A',
        ]);

        Device::create([
            'name' => 'Optimized Meter B',
            'type' => 'Energy Meter',
            'ip_address' => '127.0.0.1',
            'ruangan_id' => 'RM-PBI47-B',
        ]);

        MonitoringEnergi::create([
            'ruangan_id' => 'RM-PBI47-A',
            'bulan' => (int) now()->month,
            'tahun' => (int) now()->year,
            'konsumsi_kwh' => 25.50,
        ]);
    }

    private function insertRoom(string $id, string $name, string $status): void
    {
        $room = [
            'id' => $id,
            'nama_ruangan' => $name,
            'kapasitas' => 30,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('ruangans', 'kode')) {
            $room['kode'] = $id;
        }

        if (Schema::hasColumn('ruangans', 'lokasi')) {
            $room['lokasi'] = 'Gedung Test';
        }

        if (Schema::hasColumn('ruangans', 'lantai')) {
            $room['lantai'] = '1';
        }

        DB::table('ruangans')->insert($room);
    }

    private function loginAdmin(Browser $browser): void
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
     * PB-47 — Database Structure Optimization
     * Memastikan struktur relasi inti tersedia dan dashboard tetap bisa membaca data hasil optimasi.
     */
    public function test_pb47_database_structure_supports_dashboard_read(): void
    {
        $this->assertTrue(Schema::hasTable('ruangans'));
        $this->assertTrue(Schema::hasTable('devices'));
        $this->assertTrue(Schema::hasTable('monitoring_energis'));
        $this->assertTrue(Schema::hasTable('jadwal_listriks'));

        $this->assertTrue(Schema::hasColumn('devices', 'ruangan_id'));
        $this->assertTrue(Schema::hasColumn('devices', 'ip_address'));
        $this->assertTrue(Schema::hasColumn('monitoring_energis', 'ruangan_id'));
        $this->assertTrue(Schema::hasColumn('jadwal_listriks', 'ruangan_id'));
        $this->assertTrue(Schema::hasColumn('jadwal_listriks', 'automation_action'));
        $this->assertTrue(Schema::hasColumn('jadwal_listriks', 'schedule_status'));

        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser->visit('/dashboard')
                ->waitForText('Admin Dashboard', 15)
                ->assertPresent('#total-energy')
                ->assertPresent('#active-rooms')
                ->assertPresent('#active-devices')
                ->assertPresent('#summary-cards');
        });
    }
}