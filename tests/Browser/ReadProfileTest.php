<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ReadProfileTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::disableForeignKeyConstraints();
        User::where('email', 'admin@voltspace.id')->delete();
        Schema::enableForeignKeyConstraints();

        User::factory()->create([
            'name' => 'Admin Profile Test',
            'email' => 'admin@voltspace.id',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);
    }

    private function loginAdmin(Browser $browser): void
    {
        $browser->visit('/login')
            ->waitFor('input[name="email"]', 10)
            ->type('email', 'admin@voltspace.id')
            ->type('password', 'admin123')
            ->press('Sign In')
            ->waitForLocation('/dashboard', 15)
            ->pause(500);
    }

    /**
     * PB-45 — Read Profile
     * Memastikan user yang sudah login dapat membaca profil dari API /api/profile.
     */
    public function test_pb45_admin_can_read_profile(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser->script(<<<'JS'
                window.__pb45ProfileResult = null;

                apiFetch('/profile')
                    .then(async function (response) {
                        const body = await response.json();
                        window.__pb45ProfileResult = {
                            status: response.status,
                            body: body
                        };
                    })
                    .catch(function (error) {
                        window.__pb45ProfileResult = {
                            status: 0,
                            body: { message: error.message }
                        };
                    });
            JS);

            $browser->waitUntil('window.__pb45ProfileResult !== null', 15);

            $result = $browser->script('return window.__pb45ProfileResult;')[0];

            $this->assertSame(200, $result['status']);
            $this->assertSame('Admin Profile Test', $result['body']['name']);
            $this->assertSame('admin@voltspace.id', $result['body']['email']);
            $this->assertSame('admin', $result['body']['role']);
            $this->assertArrayNotHasKey('password', $result['body']);
            $this->assertArrayNotHasKey('remember_token', $result['body']);
        });
    }
}