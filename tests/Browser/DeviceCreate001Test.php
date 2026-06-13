<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceCreate001Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareDeviceDuskTest();
    }

    /**
     * PBI #5 TC.Device.Create.001
     */
    public function test_tc_device_create_001()
    {
        $rid = $this->testRuanganId;
        $this->browse(function (Browser $browser) use ($rid) {
            $this->loginAdmin($browser);
            $this->visitDevicesPage($browser);

            $browser->click('button[onclick="openAddDeviceModal()"]')
                ->waitForText('Add Device')
                ->type('name', 'Smart Meter #4521')
                ->type('type', 'Energy Meter')
                ->type('ip_address', '192.168.1.100');
            $this->selectRoomInDropdown($browser, 'ruangan_id', $rid);

            $browser->assertSelected('ruangan_id', $rid)
                ->click('#add-device-form button[type="submit"]')
                ->waitUntilMissing('#add-device-modal:not(.hidden)', 15)
                ->waitForText('Smart Meter #4521', 15)
                ->assertSee('Smart Meter #4521');
        });
    }
}
