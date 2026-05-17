<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceUpdate002Test extends DuskTestCase
{
    use CreatesTestRuangan;
    use DatabaseMigrations;

    protected string $testRuanganId = '';

    protected function setUp(): void
    {
        parent::setUp();

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
     * PBI #7 TC.Device.Update.002
     */
    public function test_tc_device_update_002()
    {
        $rid = $this->testRuanganId;
        Device::create([
            'name' => 'Valid Device',
            'type' => 'Type',
            'ip_address' => '10.0.0.3',
            'ruangan_id' => $rid,
        ]);

        $this->browse(function (Browser $browser) use ($rid) {
            $this->loginAdmin($browser);

            $browser->visit('/devices')
                ->waitUntilMissingText('Loading devices...')
                ->waitForText('Valid Device')
                ->click('.btn-edit-device')
                ->waitForText('Edit Device')
                ->clear('edit_name')
                ->type('edit_name', 'Hacked Device')
                ->script('
                        window.__lastAlertMessage = null;
                        window.alert = function(message) {
                            window.__lastAlertMessage = String(message);
                        };
                    ');

            $browser->script('
                        var select = document.querySelector(\'select[name="edit_ruangan_id"]\');
                        var option = document.createElement(\'option\');
                        option.value = \'99999\';
                        option.text = \'Invalid Room\';
                        select.appendChild(option);
                        select.value = \'99999\';
                    ');

            $browser->click('#edit-device-form button[type="submit"]')
                ->pause(1500);

            $alertMessages = $browser->script('return window.__lastAlertMessage;');
            $this->assertNotNull($alertMessages[0] ?? null);
            $this->assertDatabaseHas('devices', [
                'name' => 'Valid Device',
                'type' => 'Type',
                'ip_address' => '10.0.0.3',
                'ruangan_id' => $rid,
            ]);
            $this->assertDatabaseMissing('devices', [
                'name' => 'Hacked Device',
            ]);
        });
    }
}
