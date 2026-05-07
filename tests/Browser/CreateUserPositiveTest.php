<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * TC.User.Create.001
 * POSITIVE — Buat user baru dengan data valid
 */
class CreateUserPositiveTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan akun admin tanpa migrate:fresh
        User::updateOrCreate(
            ['email' => 'admin@voltspace.id'],
            [
                'name'     => 'Admin Voltspace',
                'password' => bcrypt('admin123'),
                'role'     => 'admin',
            ]
        );

        // Hapus user target dari run sebelumnya (agar tidak duplikat)
        User::where('email', 'testuser@example.com')->delete();
    }

    private function loginAdmin(Browser $browser): void
    {
        $browser->visit('/login')
                ->waitFor('input[name="email"]', 10)
                ->type('email', 'admin@voltspace.id')
                ->type('password', 'admin123')
                ->press('Sign In')
                ->waitForLocation('/rooms', 15);
    }

    public function test_tc_user_create_001(): void
    {
        $this->browse(function (Browser $browser) {

            // 1. Login sebagai admin
            $this->loginAdmin($browser);

            // 2. Buka halaman Users
            $browser->visit('/users')
                    ->waitForText('Add User', 10);

            // 3. Buka modal Add User via JavaScript (menghindari StaleElementReference)
            $browser->script("document.querySelector(\"button[onclick='openModal()']\").click();");

            // 4. Tunggu modal muncul
            $browser->waitFor('#user-modal:not(.hidden)', 10);

            // 5. Isi form dengan data valid
            $browser->type('#user-form input[name="name"]',     'Test User')
                    ->type('#user-form input[name="email"]',    'testuser@example.com')
                    ->type('#user-form input[name="password"]', 'password123')
                    ->select('#user-form select[name="role"]',  'mahasiswa');

            // 6. Submit form
            $browser->script("document.querySelector('#user-form button[type=\"submit\"]').click();");

            // 7. Tunggu modal tertutup (sukses) lalu data muncul di tabel
            $browser->waitUntilMissing('#user-modal:not(.hidden)', 10)
                    ->waitForText('testuser@example.com', 15)
                    ->assertSee('Test User')
                    ->assertPathIs('/users');

            // 8. Verifikasi data tersimpan di database
            $this->assertDatabaseHas('users', [
                'email' => 'testuser@example.com',
                'name'  => 'Test User',
            ]);
        });
    }
}