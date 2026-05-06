<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DeviceCreate002Test extends DuskTestCase
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
     * PBI #5 TC.Device.Create.002
     */
    public function test_tc_device_create_002()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser->visit('/devices')
                    ->waitForText('Devices')
                    ->click('button[onclick="openAddDeviceModal()"]')
                    ->waitForText('Add Device')
                    ->type('type', 'Energy Meter')
                    ->type('ip_address', '192.168.1.101')
                    ->select('ruangan_id', 'R-001')
                    ->clear('name')
                    ->click('#add-device-form button[type="submit"]')
                    ->pause(1000)
                    ->assertSee('Add Device')
                    ->assertDontSee('192.168.1.101');
        });
    }
}
