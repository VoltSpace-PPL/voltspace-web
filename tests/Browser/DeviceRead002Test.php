<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceRead002Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareDeviceDuskTest();
    }

    /**
     * PBI #6 TC.Device.Read.002
     */
    public function test_tc_device_read_002()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $this->visitDevicesPage($browser)
                ->waitForText('No devices found', 15)
                ->assertSee('No devices found');
        });
    }
}
