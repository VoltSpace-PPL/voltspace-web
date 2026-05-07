<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * TC.User.Delete.001
 * POSITIVE — Klik delete & konfirmasi, user hilang dari tabel dan database
 */
class DeleteUserPositiveTest extends DuskTestCase
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

        // Siapkan user yang akan dihapus
        $user = User::updateOrCreate(
            ['email' => 'delete@example.com'],
            [
                'name'     => 'To Be Deleted',
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

    public function test_tc_user_delete_001(): void
    {
        $this->browse(function (Browser $browser) {

            // 1. Login sebagai admin
            $this->loginAdmin($browser);

            // 2. Buka halaman Users, tunggu data muncul
            $browser->visit('/users')
                    ->waitForText('To Be Deleted', 15);

            // 3. Klik tombol Delete untuk user target
            $id = $this->targetUserId;
            $browser->click(".btn-delete-user[data-delete-uid='{$id}']")
                    ->waitFor('#delete-user-modal:not(.hidden)', 10);

            // 4. Konfirmasi penghapusan dengan klik "Delete User"
            $browser->click('#confirm-delete-user-btn');

            // 5. Tunggu user hilang dari tabel
            $browser->waitUntilMissingText('To Be Deleted', 15)
                    ->assertDontSee('To Be Deleted');

            // 6. Verifikasi user sudah dihapus dari database
            $this->assertDatabaseMissing('users', [
                'id' => $id,
            ]);
        });
    }
}
