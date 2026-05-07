<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * TC.User.Create.002
 * NEGATIVE — Submit form kosong, validasi harus gagal
 */
class CreateUserNegativeTest extends DuskTestCase
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

    public function test_tc_user_create_002_empty_field(): void
    {
        $this->browse(function (Browser $browser) {

            // 1. Login sebagai admin
            $this->loginAdmin($browser);

            // 2. Buka halaman Users
            $browser->visit('/users')
                    ->waitForText('Add User', 10);

            // 3. Buka modal via JS
            $browser->script("openModal();");

            // 4. Tunggu modal terbuka
            $browser->waitFor('#user-modal:not(.hidden)', 10);

            // 5. Biarkan semua field KOSONG, langsung klik submit
            $browser->script("document.querySelector('#user-form button[type=\"submit\"]').click();");

            // 6. Tunggu sebentar
            $browser->pause(1500);

            // 7. ASSERT NEGATIVE: modal masih terbuka (validasi HTML5 mencegah submit)
            $browser->assertVisible('#user-modal');

            // 8. Pastikan tidak ada data invalid yang masuk ke database
            $this->assertDatabaseMissing('users', [
                'email' => 'invalid-email',
            ]);
        });
    }
}