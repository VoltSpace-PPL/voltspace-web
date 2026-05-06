<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DeviceDelete002Test extends DuskTestCase
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
     * PBI #8 TC.Device.Delete.002
     */
    public function test_tc_device_delete_002()
    {
        Device::create([
            'name' => 'Device Session Delete',
            'type' => 'Session Delete Type',
            'ip_address' => '10.0.0.5',
            'ruangan_id' => 'R-001'
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser->visit('/devices')
                    ->waitUntilMissingText('Loading devices...')
                    ->waitForText('Device Session Delete')
                    ->click('.btn-delete-device')
                    ->waitForText('Delete Device?')
                    ->script("localStorage.removeItem('token');");
                    
            $browser->script("document.getElementById('confirm-delete-device-btn').click();");

            $browser->waitForLocation('/login')
                    ->assertPathIs('/login');
        });
    }
}
