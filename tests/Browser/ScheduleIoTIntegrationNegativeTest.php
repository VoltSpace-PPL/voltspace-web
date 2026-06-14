<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ScheduleIoTIntegrationNegativeTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::disableForeignKeyConstraints();

        User::where('email', 'admin@voltspace.id')->delete();

        Schema::enableForeignKeyConstraints();

        User::factory()->create([
            'name' => 'Admin Schedule Negative Test',
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
     * TC-PB46-002 — Negative
     * Admin tidak bisa membuat jadwal listrik dengan ruangan_id tidak valid.
     */
    public function test_negative_admin_cannot_create_schedule_with_invalid_room(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);

            $browser->script(<<<'JS'
                window.__scheduleNegativeResult = null;

                (async function () {
                    try {
                        const today = new Date()
                            .toLocaleDateString('en-US', { weekday: 'long' })
                            .toLowerCase();

                        const res = await apiFetch('/jadwal-listrik', {
                            method: 'POST',
                            body: JSON.stringify({
                                ruangan_id: 'RM-NOT-FOUND',
                                selected_days: [today],
                                start_time: '08:00',
                                end_time: '17:00',
                                automation_action: 'on',
                                schedule_status: 'active'
                            })
                        });

                        const body = await res.json().catch(() => ({}));

                        window.__scheduleNegativeResult = {
                            status: res.status,
                            body: body
                        };
                    } catch (error) {
                        window.__scheduleNegativeResult = {
                            status: 0,
                            body: {
                                message: error.message
                            }
                        };
                    }
                })();
            JS);

            $browser->waitUntil('window.__scheduleNegativeResult !== null', 15);

            $result = $browser->script('return window.__scheduleNegativeResult;')[0];

            $this->assertSame(422, $result['status']);
            $this->assertArrayHasKey('errors', $result['body']);
            $this->assertArrayHasKey('ruangan_id', $result['body']['errors']);
        });
    }
}