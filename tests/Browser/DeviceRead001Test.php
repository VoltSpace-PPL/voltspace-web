<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceRead001Test extends DuskTestCase
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

            $browser->visit('/devices')
                ->waitUntilMissingText('Loading devices...')
                ->waitForText('Visible Device')
                ->assertSee('Visible Device')
                ->assertSee('IoT device management and monitoring');
        });
    }
}
