<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestRuangan;
use Tests\DuskTestCase;

class DeviceRead002Test extends DuskTestCase
{
    use CreatesTestRuangan;
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'email' => 'admin@voltspace.id',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);

        $this->makeTestRuangan();
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
     * PBI #6 TC.Device.Read.002
     */
    public function test_tc_device_read_002()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser->visit('/devices')
                ->waitUntilMissingText('Loading devices...')
                ->assertSee('No devices found');
        });
    }
}
