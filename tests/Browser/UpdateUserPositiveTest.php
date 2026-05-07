<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * TC.User.Update.001
 * POSITIVE — Update nama & email user dengan data valid
 */
class UpdateUserPositiveTest extends DuskTestCase
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

        // Siapkan user yang akan di-update
        $user = User::updateOrCreate(
            ['email' => 'old@example.com'],
            [
                'name'     => 'Old Name',
                'password' => bcrypt('password123'),
                'role'     => 'mahasiswa',
            ]
        );

        // Reset ke nilai awal jika user sudah pernah di-update
        $user->update(['name' => 'Old Name', 'email' => 'old@example.com']);

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

    public function test_tc_user_update_001(): void
    {
        $this->browse(function (Browser $browser) {

            // 1. Login sebagai admin
            $this->loginAdmin($browser);

            // 2. Buka halaman Users, tunggu data muncul
            $browser->visit('/users')
                    ->waitForText('Old Name', 15);

            // 3. Klik tombol Edit untuk user target
            $id = $this->targetUserId;
            $browser->click(".btn-edit-user[data-edit-uid='{$id}']")
                    ->waitFor('#edit-user-modal:not(.hidden)', 10)
                    ->pause(500); // Tunggu JS mengisi form

            // 4. Ubah nama dan email
            $browser->clear('#edit-user-form input[name="edit_name"]')
                    ->type('#edit-user-form input[name="edit_name"]',  'New Name')
                    ->clear('#edit-user-form input[name="edit_email"]')
                    ->type('#edit-user-form input[name="edit_email"]', 'new@example.com');

            // 5. Submit via JS
            $browser->script("document.querySelector('#edit-user-form button[type=\"submit\"]').click();");

            // 6. Tunggu modal tertutup & data baru muncul di tabel
            $browser->waitUntilMissing('#edit-user-modal:not(.hidden)', 10)
                    ->waitForText('New Name', 15)
                    ->assertSee('New Name')
                    ->assertDontSee('Old Name');

            // 7. Verifikasi perubahan di database
            $this->assertDatabaseHas('users', [
                'id'    => $id,
                'name'  => 'New Name',
                'email' => 'new@example.com',
            ]);
        });
    }
}
