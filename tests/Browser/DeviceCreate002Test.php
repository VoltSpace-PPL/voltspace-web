<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceCreate002Test extends DuskTestCase
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
     * PBI #5 TC.Device.Create.002
     */
    public function test_tc_device_create_002()
    {
        $rid = $this->testRuanganId;
        $this->browse(function (Browser $browser) use ($rid) {
            $this->loginAdmin($browser);

            $browser->visit('/devices')
                ->waitForText('Devices')
                ->click('button[onclick="openAddDeviceModal()"]')
                ->waitForText('Add Device')
                ->type('type', 'Energy Meter')
                ->type('ip_address', '192.168.1.101')
                ->select('ruangan_id', $rid)
                ->clear('name')
                ->click('#add-device-form button[type="submit"]')
                ->pause(1000)
                ->assertSee('Add Device')
                ->assertDontSee('192.168.1.101');
        });
    }
}
