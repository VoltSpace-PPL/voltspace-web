<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthLogin001Test extends DuskTestCase
{
    public function test_tc_auth_login_001()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->assertSee('Email Address')
                    ->type('email', 'admin@voltspace.id')
                    ->type('password', 'admin123')
                    ->press('Sign In')
                    ->waitForLocation('/rooms', 15)
                    ->assertPathIs('/rooms');
        });
    }
}
