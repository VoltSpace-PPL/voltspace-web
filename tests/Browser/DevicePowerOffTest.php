<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DevicePowerOffTest extends DuskTestCase
{
    public function test_user_can_turn_off_device(): void
    {
        $this->browse(function (Browser $browser) {

            $browser
                ->visit('/login')
                ->pause(2000)
                ->type('email', 'admin@voltspace.com')
                ->type('password', 'password')
                ->press('Login')
                ->pause(3000)
                ->visit('/rooms')
                ->pause(5000)
                ->assertSee('Rooms');
            $browser->script("
                const toggle = document.querySelector('.switch input');

                if(toggle && toggle.checked){
                    toggle.click();
                }
            ");
            $browser->pause(5000);
            $browser->assertSee('OFF');
        });
    }
}