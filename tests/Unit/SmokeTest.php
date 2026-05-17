<?php

namespace Tests\Unit;

use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_health_endpoint(): void
    {
        $this->get('/health')->assertOk();
    }

    public function test_api_unauthenticated_returns_json_without_accept_header(): void
    {
        $response = $this->get('/api/profile');

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
