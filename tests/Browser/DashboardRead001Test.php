<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class DashboardRead001Test extends DuskTestCase
{
    public function test_dashboard_displays_metrics_summary()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'admin@voltspace.id')
                    ->type('password', 'admin123')
                    ->press('Sign In')
                    ->waitForLocation('/rooms')
                    ->visit('/dashboard')
                    ->waitForText('Admin Dashboard')
                    ->assertPresent('#total-energy')
                    ->assertPresent('#active-rooms')
                    ->assertPresent('#active-devices')
                    ->assertPresent('#summary-cards');
        });
    }
}
