<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DatabaseStructureOptimizationNegativeTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::disableForeignKeyConstraints();

        User::where('email', 'admin@voltspace.id')->delete();

        Schema::enableForeignKeyConstraints();

        User::factory()->create([
            'name' => 'Admin DB Negative Test',
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
     * TC-PB47-002 — Negative
     * Sistem menolak device dengan relasi ruangan_id tidak valid.
     */
    public function test_negative_invalid_device_room_relation_is_rejected(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser->script(<<<'JS'
                window.__dbRelationNegativeResult = null;

                (async function () {
                    try {
                        const res = await apiFetch('/devices', {
                            method: 'POST',
                            body: JSON.stringify({
                                name: 'Invalid Device Relation',
                                type: 'Energy Meter',
                                ip_address: '127.0.0.1',
                                ruangan_id: 'RM-NOT-FOUND'
                            })
                        });

                        const body = await res.json().catch(() => ({}));

                        window.__dbRelationNegativeResult = {
                            status: res.status,
                            body: body
                        };
                    } catch (error) {
                        window.__dbRelationNegativeResult = {
                            status: 0,
                            body: {
                                message: error.message
                            }
                        };
                    }
                })();
            JS);

            $browser->waitUntil('window.__dbRelationNegativeResult !== null', 15);

            $result = $browser->script('return window.__dbRelationNegativeResult;')[0];

            $this->assertSame(422, $result['status']);
            $this->assertArrayHasKey('errors', $result['body']);
            $this->assertArrayHasKey('ruangan_id', $result['body']['errors']);
        });
    }
}