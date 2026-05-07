<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthLogin002Test extends DuskTestCase
{
    public function test_tc_auth_login_002()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'admin@voltspace.id')
                    ->type('password', 'salahpassword')
                    ->press('Sign In')
                    ->waitForDialog()
                    ->acceptDialog()
                    ->assertPathIs('/login');
        });
    }
}
