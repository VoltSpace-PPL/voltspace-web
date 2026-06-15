<?php

namespace Tests\Browser;

use App\Models\Device;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceDelete002Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareDeviceDuskTest();
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
            'ruangan_id' => $this->testRuanganId,
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser = $this->visitDevicesPage($browser);
            $browser->waitForText('Device Session Delete', 15)
                ->click('.btn-delete-device')
                ->waitForText('Delete Device?')
                ->script("localStorage.removeItem('token');");

            $browser->script("document.getElementById('confirm-delete-device-btn').click();");

            $browser->waitForLocation('/login', 15)
                ->assertPathIs('/login');
        });
    }
}
