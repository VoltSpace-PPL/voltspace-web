<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TrendRead001Test extends DuskTestCase
{
    public function test_trend_chart_loading_state()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'admin@voltspace.id')
                    ->type('password', 'admin123')
                    ->press('Sign In')
                    ->waitForLocation('/rooms')
                    ->visit('/dashboard');

            // Memastikan indikator loading muncul sebelum data terisi
            $browser->assertPresent('#trend-loading')
                    ->waitUntilMissingText('Loading data...', 10);
        });
    }
}
