<?php

namespace Tests\Browser;

use App\Models\Device;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceUpdate002Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareDeviceDuskTest();
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
            $this->visitDevicesPage($browser);

            $browser->waitForText('Valid Device', 15)
                ->click('.btn-edit-device')
                ->waitForText('Edit Device')
                ->pause(1000)
                ->clear('edit_name')
                ->type('edit_name', 'Hacked Device')
                ->script('
                    window.__lastAlertMessage = null;
                    window.alert = function(message) {
                        window.__lastAlertMessage = String(message);
                    };
                    var select = document.querySelector("select[name=\"edit_ruangan_id\"]");
                    var option = document.createElement("option");
                    option.value = "99999";
                    option.text = "Invalid Room";
                    select.appendChild(option);
                    select.value = "99999";
                ');

            $browser->click('#edit-device-form button[type="submit"]')
                ->pause(2000);

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
