<?php

namespace Tests\Browser;

use App\Models\Device;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceUpdate001Test extends DuskTestCase
{
    use CreatesTestRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareDeviceDuskTest();
    }

    /**
     * PBI #7 TC.Device.Update.001
     */
    public function test_tc_device_update_001()
    {
        $rid = $this->testRuanganId;
        Device::create([
            'name' => 'Old Device Name',
            'type' => 'Old Type',
            'ip_address' => '10.0.0.1',
            'ruangan_id' => $rid,
        ]);

        $this->browse(function (Browser $browser) use ($rid) {
            $this->loginAdmin($browser);
            $this->visitDevicesPage($browser);

            $browser->waitForText('Old Device Name', 15)
                ->click('.btn-edit-device')
                ->waitForText('Edit Device')
                ->pause(1000)
                ->clear('edit_name')
                ->type('edit_name', 'Updated Device Name')
                ->clear('edit_type')
                ->type('edit_type', 'Updated Type')
                ->clear('edit_ip_address')
                ->type('edit_ip_address', '10.0.0.2');
            $this->selectRoomInDropdown($browser, 'edit_ruangan_id', $rid);

            $browser->assertSelected('edit_ruangan_id', $rid)
                ->click('#edit-device-form button[type="submit"]')
                ->waitUntilMissing('#edit-device-modal:not(.hidden)', 15)
                ->waitForText('Updated Device Name', 15)
                ->assertSee('Updated Device Name')
                ->assertDontSee('Old Device Name');
        });
    }
}
