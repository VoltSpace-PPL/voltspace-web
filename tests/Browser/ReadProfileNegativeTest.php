<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ReadProfileNegativeTest extends DuskTestCase
{
    /**
     * TC-PB45-002 — Negative
     * Guest/tanpa token tidak boleh membaca profile.
     */
    public function test_negative_guest_cannot_read_profile(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitFor('input[name="email"]', 10);

            $browser->script(<<<'JS'
                localStorage.removeItem('token');

                window.__profileNegativeResult = null;

                fetch('/api/profile', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                    .then(async function (response) {
                        const body = await response.json().catch(() => ({}));

                        window.__profileNegativeResult = {
                            status: response.status,
                            body: body
                        };
                    })
                    .catch(function (error) {
                        window.__profileNegativeResult = {
                            status: 0,
                            body: {
                                message: error.message
                            }
                        };
                    });
            JS);

            $browser->waitUntil('window.__profileNegativeResult !== null', 15);

            $result = $browser->script('return window.__profileNegativeResult;')[0];

            $this->assertSame(401, $result['status']);
            $this->assertSame('Unauthenticated.', $result['body']['message']);
        });
    }
}