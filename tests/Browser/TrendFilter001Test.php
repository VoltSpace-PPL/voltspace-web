<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TrendFilter001Test extends DuskTestCase
{
    public function test_energy_trend_navigation_and_chart_display()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'admin@voltspace.id')
                    ->type('password', 'admin123')
                    ->press('Sign In')
                    ->waitForLocation('/rooms')
                    ->visit('/dashboard')
                    ->waitFor('#trend-chart')
                    ->assertPresent('#trend-chart')
                    ->assertVisible('#trend-chart');
            
            $currentYear = date('Y');
            $browser->assertSeeIn('#trend-year', $currentYear)
                    ->click('#trend-prev')
                    ->waitForText(date('Y', strtotime('-1 year')), 5)
                    ->assertPresent('#trend-chart')
                    ->click('#trend-next')
                    ->waitForText($currentYear, 5)
                    ->assertPresent('#trend-chart');
        });
    }
}
