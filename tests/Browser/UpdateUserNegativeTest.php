<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * TC.User.Update.002
 * NEGATIVE — Update email dengan format tidak valid, data tidak boleh berubah
 */
class UpdateUserNegativeTest extends DuskTestCase
{
    private int $targetUserId;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan admin
        User::updateOrCreate(
            ['email' => 'admin@voltspace.id'],
            [
                'name'     => 'Admin Voltspace',
                'password' => bcrypt('admin123'),
                'role'     => 'admin',
            ]
        );

        // Siapkan user yang akan dicoba di-update dengan email invalid
        $user = User::updateOrCreate(
            ['email' => 'valid@example.com'],
            [
                'name'     => 'Valid Name',
                'password' => bcrypt('password123'),
                'role'     => 'mahasiswa',
            ]
        );

        $this->targetUserId = $user->id;
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

    public function test_tc_user_update_002(): void
    {
        $this->browse(function (Browser $browser) {

            // 1. Login sebagai admin
            $this->loginAdmin($browser);

            // 2. Buka halaman Users, tunggu data muncul
            $browser->visit('/users')
                    ->waitForText('Valid Name', 15);

            // 3. Klik tombol Edit untuk user target
            $id = $this->targetUserId;
            $browser->click(".btn-edit-user[data-edit-uid='{$id}']")
                    ->waitFor('#edit-user-modal:not(.hidden)', 10)
                    ->pause(500);

            // 4. Masukkan email dengan format tidak valid
            $browser->clear('#edit-user-form input[name="edit_email"]')
                    ->type('#edit-user-form input[name="edit_email"]', 'invalid-email-format');

            // 5. Submit form
            $browser->script("document.querySelector('#edit-user-form button[type=\"submit\"]').click();");

            // 6. Tunggu sebentar
            $browser->pause(1500);

            // 7. ASSERT NEGATIVE: modal edit masih terbuka (validasi HTML5 mencegah submit)
            $browser->assertVisible('#edit-user-modal');

            // 8. Pastikan data di database tidak berubah
            $this->assertDatabaseHas('users', [
                'id'    => $id,
                'email' => 'valid@example.com',
            ]);
        });
    }
}
