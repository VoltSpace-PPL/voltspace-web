<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Ruangan;

class RoomsRead002Test extends DuskTestCase
{
    public function test_rooms_handle_null_consumption_gracefully()
    {
        Ruangan::updateOrCreate(
            ['id' => 'R-EMPTY-01'],
            [
                'nama_ruangan' => 'Empty Data Room',
                'lokasi' => 'Building B',
                'kapasitas' => 5,
                'status' => 'tersedia'
            ]
        );

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'admin@voltspace.id')
                    ->type('password', 'admin123')
                    ->press('Sign In')
                    ->waitForLocation('/rooms')
                    ->waitForText('Empty Data Room', 15)
                    ->assertSee('0 kWh'); // Mengikuti logika asli aplikasi (default 0)
        });
    }
}
