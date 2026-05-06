<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DeviceUpdate002Test extends DuskTestCase
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
     * PBI #7 TC.Device.Update.002
     */
    public function test_tc_device_update_002()
    {
        Device::create([
            'name' => 'Valid Device',
            'type' => 'Type',
            'ip_address' => '10.0.0.3',
            'ruangan_id' => 'R-001'
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser->visit('/devices')
                    ->waitUntilMissingText('Loading devices...')
                    ->waitForText('Valid Device')
                    ->click('.btn-edit-device')
                    ->waitForText('Edit Device')
                    ->clear('edit_name')
                    ->type('edit_name', 'Hacked Device')
                    ->script("
                        window.__lastAlertMessage = null;
                        window.alert = function(message) {
                            window.__lastAlertMessage = String(message);
                        };
                    ");

            $browser->script("
                        var select = document.querySelector('select[name=\"edit_ruangan_id\"]');
                        var option = document.createElement('option');
                        option.value = '99999';
                        option.text = 'Invalid Room';
                        select.appendChild(option);
                        select.value = '99999';
                    ");

            $browser->click('#edit-device-form button[type="submit"]')
                    ->pause(1500);

            $alertMessages = $browser->script("return window.__lastAlertMessage;");
            $this->assertNotNull($alertMessages[0] ?? null);
            $this->assertDatabaseHas('devices', [
                'name' => 'Valid Device',
                'type' => 'Type',
                'ip_address' => '10.0.0.3',
                'ruangan_id' => 'R-001',
            ]);
            $this->assertDatabaseMissing('devices', [
                'name' => 'Hacked Device',
            ]);
        });
    }
}
