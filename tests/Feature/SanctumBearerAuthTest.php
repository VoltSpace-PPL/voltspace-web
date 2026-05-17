<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SanctumBearerAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_bearer_token_authenticates_api_route(): void
    {
        $user = User::factory()->create([
            'email' => 'apitest@example.com',
            'password' => 'password123',
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('email', 'apitest@example.com');
    }

    public function test_lowercase_bearer_prefix_authenticates(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('t2')->plainTextToken;

        $this->withHeader('Authorization', 'bearer '.$token)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('id', $user->id);
    }
}
