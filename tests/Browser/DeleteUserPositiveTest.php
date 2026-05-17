<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * TC.User.Delete.001
 * POSITIVE — Hanya super admin dapat menghapus user lain.
 */
class DeleteUserPositiveTest extends DuskTestCase
{
    private int $targetUserId;

    protected function setUp(): void
    {
        parent::setUp();

        User::updateOrCreate(
            ['email' => 'superadmin@voltspace.id'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('super123'),
                'role' => 'super_admin',
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'delete@example.com'],
            [
                'name' => 'To Be Deleted',
                'password' => bcrypt('password123'),
                'role' => 'mahasiswa',
            ]
        );

        $this->targetUserId = $user->id;
    }

    private function loginSuperAdmin(Browser $browser): void
    {
        $browser->visit('/login')
            ->waitFor('input[name="email"]', 10)
            ->type('email', 'superadmin@voltspace.id')
            ->type('password', 'super123')
            ->press('Sign In')
            ->waitForLocation('/rooms', 15);
    }

    public function test_tc_user_delete_001(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginSuperAdmin($browser);

            $browser->visit('/users')
                ->waitForText('To Be Deleted', 15);

            $id = $this->targetUserId;
            $browser->click(".btn-delete-user[data-delete-uid='{$id}']")
                ->waitFor('#delete-user-modal:not(.hidden)', 10);

            $browser->click('#confirm-delete-user-btn');

            $browser->waitUntilMissingText('To Be Deleted', 15)
                ->assertDontSee('To Be Deleted');

            $this->assertDatabaseMissing('users', [
                'id' => $id,
            ]);
        });
    }
}
