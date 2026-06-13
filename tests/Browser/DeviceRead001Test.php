<?php

namespace Tests\Browser;

use App\Models\Device;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceRead001Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareDeviceDuskTest();
    }

    /**
     * PBI #6 TC.Device.Read.001
     */
    public function test_tc_device_read_001()
    {
        Device::create([
            'name' => 'Visible Device',
            'type' => 'Meter',
            'ip_address' => '10.0.0.5',
            'ruangan_id' => $this->testRuanganId,
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $this->visitDevicesPage($browser)
                ->waitForText('Visible Device', 15)
                ->assertSee('Visible Device')
                ->assertSee('IoT device management and monitoring');
        });
    }
}
