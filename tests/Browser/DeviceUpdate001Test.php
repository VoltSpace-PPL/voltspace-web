<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceUpdate001Test extends DuskTestCase
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

            $browser->visit('/devices')
                ->waitUntilMissingText('Loading devices...')
                ->waitForText('Old Device Name')
                ->click('.btn-edit-device')
                ->waitForText('Edit Device')
                ->clear('edit_name')
                ->type('edit_name', 'Updated Device Name')
                ->clear('edit_type')
                ->type('edit_type', 'Updated Type')
                ->clear('edit_ip_address')
                ->type('edit_ip_address', '10.0.0.2')
                ->select('edit_ruangan_id', $rid)
                ->click('#edit-device-form button[type="submit"]')
                ->waitUntilMissingText('Edit Device')
                ->waitForText('Updated Device Name')
                ->assertSee('Updated Device Name')
                ->assertDontSee('Old Device Name');
        });
    }
}
