<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * TC.User.Read.002
 * NEGATIVE — Akses halaman /users tanpa login harus diarahkan ke /login
 * dan data user tidak boleh terlihat
 */
class ReadUserNegativeTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan user yang ada di database (sebagai data yang seharusnya tersembunyi)
        User::updateOrCreate(
            ['email' => 'johndoe@example.com'],
            [
                'name'     => 'John Doe',
                'password' => bcrypt('password123'),
                'role'     => 'mahasiswa',
            ]
        );
    }

    public function test_tc_user_read_002(): void
    {
        $this->browse(function (Browser $browser) {

            // 1. Pastikan tidak ada sesi login (hapus localStorage token)
            $browser->visit('/login')
                    ->script("localStorage.removeItem('token');");

            // 2. Coba akses langsung ke halaman /users tanpa login
            $browser->visit('/users');

            // 3. ASSERT NEGATIVE: Tunggu sebentar lalu pastikan user
            //    diarahkan kembali ke /login ATAU data user tidak tampil
            $browser->pause(2000);

            // Cek apakah diarahkan ke login atau tabel gagal memuat data
            $currentPath = $browser->driver->getCurrentURL();

            if (str_contains($currentPath, '/login')) {
                // Jika redirect ke login — assertion utama
                $browser->assertPathIs('/login')
                        ->assertDontSee('John Doe');
            } else {
                // Jika halaman /users tetap terbuka tapi gagal load data
                // karena tidak ada token, tabel harus menampilkan pesan error
                $browser->assertPathIs('/users')
                        ->assertDontSee('John Doe')
                        ->assertSeeIn('#users-table-body', 'Failed to load users');
            }
        });
    }
}
