<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DashboardRead002Test extends DuskTestCase
{
    public function test_dashboard_protected_from_guest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/dashboard')
                    ->assertPathIs('/login')
                    ->assertSee('Welcome Back');
        });
    }
}
