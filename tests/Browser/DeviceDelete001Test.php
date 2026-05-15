<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceDelete001Test extends DuskTestCase
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

            $browser->visit('/devices')
                ->waitUntilMissingText('Loading devices...')
                ->waitForText('Device To Delete')
                ->click('.btn-delete-device')
                ->waitForText('Delete Device?')
                ->click('#confirm-delete-device-btn')
                ->waitUntilMissingText('Delete Device?')
                ->pause(1000)
                ->assertDontSee('Device To Delete');
        });
    }
}
