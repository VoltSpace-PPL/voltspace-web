<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * TC.User.Delete.002
 * NEGATIVE — Klik delete lalu Cancel, user tetap ada di tabel dan database
 */
class DeleteUserNegativeTest extends DuskTestCase
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

        // Siapkan user yang TIDAK boleh terhapus
        $user = User::updateOrCreate(
            ['email' => 'nodelete@example.com'],
            [
                'name'     => 'Not Deleted',
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

    public function test_tc_user_delete_002(): void
    {
        $this->browse(function (Browser $browser) {

            // 1. Login sebagai admin
            $this->loginAdmin($browser);

            // 2. Buka halaman Users, tunggu data muncul
            $browser->visit('/users')
                    ->waitForText('Not Deleted', 15);

            // 3. Klik tombol Delete untuk user target
            $id = $this->targetUserId;
            $browser->click(".btn-delete-user[data-delete-uid='{$id}']")
                    ->waitFor('#delete-user-modal:not(.hidden)', 10);

            // 4. BATALKAN penghapusan dengan klik "Cancel"
            $browser->click("#delete-user-modal button[onclick='closeDeleteUserModal()']");

            // 5. Tunggu modal tertutup
            $browser->waitUntilMissing('#delete-user-modal:not(.hidden)', 10);

            // 6. ASSERT NEGATIVE: user masih ada di tabel
            $browser->assertSee('Not Deleted');

            // 7. Pastikan data masih ada di database
            $this->assertDatabaseHas('users', [
                'id' => $id,
            ]);
        });
    }
}
