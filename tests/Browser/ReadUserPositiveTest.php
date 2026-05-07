<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * TC.User.Read.001
 * POSITIVE — Data user tampil di tabel setelah login
 */
class ReadUserPositiveTest extends DuskTestCase
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

        // Siapkan target user yang akan dicek keberadaannya di tabel
        $user = User::updateOrCreate(
            ['email' => 'johndoe@example.com'],
            [
                'name'     => 'John Doe',
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

    public function test_tc_user_read_001(): void
    {
        $this->browse(function (Browser $browser) {

            // 1. Login sebagai admin
            $this->loginAdmin($browser);

            // 2. Buka halaman Users dan tunggu data dari API dimuat
            $browser->visit('/users')
                    ->waitForText('John Doe', 15)
                    ->assertSee('John Doe')
                    ->assertSee('johndoe@example.com');

            // 3. Pastikan tombol Edit & Delete muncul untuk user tersebut
            $id = $this->targetUserId;
            $browser->assertPresent(".btn-edit-user[data-edit-uid='{$id}']")
                    ->assertPresent(".btn-delete-user[data-delete-uid='{$id}']");
        });
    }
}
