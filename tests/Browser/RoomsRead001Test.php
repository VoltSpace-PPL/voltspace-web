<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Ruangan;

class RoomsRead001Test extends DuskTestCase
{
    public function test_rooms_page_displays_consumption_charts()
    {
        // Pastikan ada minimal 1 ruangan untuk dites grafiknya
        Ruangan::updateOrCreate(
            ['id' => 'R-TEST-01'],
            [
                'nama_ruangan' => 'Test Room 01',
                'lokasi' => 'Building A',
                'kapasitas' => 10,
                'status' => 'tersedia'
            ]
        );

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'admin@voltspace.id')
                    ->type('password', 'admin123')
                    ->press('Sign In')
                    ->waitForLocation('/rooms')
                    ->waitForText('Test Room 01', 15)
                    ->assertPresent('canvas');
        });
    }
}
