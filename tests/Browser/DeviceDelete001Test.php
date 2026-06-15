<?php

namespace Tests\Browser;

use App\Models\Device;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceDelete001Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareDeviceDuskTest();
    }

    /**
     * PBI #8 TC.Device.Delete.001
     */
    public function test_tc_device_delete_001()
    {
        Device::create([
            'name' => 'Device To Delete',
            'type' => 'Delete Type',
            'ip_address' => '10.0.0.4',
            'ruangan_id' => $this->testRuanganId,
        ]);

        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);
            $this->visitDevicesPage($browser);

            $browser->waitForText('Device To Delete', 15)
                ->click('.btn-delete-device')
                ->waitForText('Delete Device?')
                ->click('#confirm-delete-device-btn')
                ->waitUntilMissing('#delete-device-modal:not(.hidden)', 15)
                ->waitUntilMissingText('Device To Delete', 15)
                ->assertDontSee('Device To Delete');
        });
    }
}
