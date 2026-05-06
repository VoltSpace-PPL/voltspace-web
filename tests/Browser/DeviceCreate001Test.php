<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DeviceCreate001Test extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        
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
     * PBI #5 TC.Device.Create.001
     */
    public function test_tc_device_create_001()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser->visit('/devices')
                    ->waitForText('Devices')
                    ->click('button[onclick="openAddDeviceModal()"]')
                    ->waitForText('Add Device')
                    ->type('name', 'Smart Meter #4521')
                    ->type('type', 'Energy Meter')
                    ->type('ip_address', '192.168.1.100')
                    ->waitFor('select[name="ruangan_id"] option[value="R-001"]')
                    ->script("document.querySelector('select[name=\"ruangan_id\"]').value = 'R-001';");

            $browser->assertSelected('ruangan_id', 'R-001')
                    ->click('#add-device-form button[type="submit"]')
                    ->waitUntilMissing('#add-device-modal:not(.hidden)')
                    ->waitForText('Smart Meter #4521')
                    ->assertSee('Smart Meter #4521');
        });
    }
}
