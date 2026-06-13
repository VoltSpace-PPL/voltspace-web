<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceCreate002Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareDeviceDuskTest();
    }

    /**
     * PBI #5 TC.Device.Create.002
     */
    public function test_tc_device_create_002()
    {
        $rid = $this->testRuanganId;
        $this->browse(function (Browser $browser) use ($rid) {
            $this->loginAdmin($browser);
            $this->visitDevicesPage($browser);

            $browser->click('button[onclick="openAddDeviceModal()"]')
                ->waitForText('Add Device')
                ->type('type', 'Energy Meter')
                ->type('ip_address', '192.168.1.101');
            $this->selectRoomInDropdown($browser, 'ruangan_id', $rid);

            $browser->clear('name')
                ->click('#add-device-form button[type="submit"]')
                ->pause(1000)
                ->assertSee('Add Device')
                ->assertDontSee('192.168.1.101');
        });
    }
}
