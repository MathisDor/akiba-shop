<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\SeedsShopData;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase, SeedsShopData;

    public function test_login_rejects_bad_credentials(): void
    {
        $user = $this->makeClient(['password' => bcrypt('correct-password')]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_login_returns_a_token_and_only_whitelisted_user_fields(): void
    {
        $user = $this->makeClient(['password' => bcrypt('correct-password')]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['user' => ['id', 'name', 'email', 'phone', 'created_at'], 'token'])
            ->assertJsonMissingPath('user.password')
            ->assertJsonMissingPath('user.remember_token')
            ->assertJsonMissingPath('user.role_id');
    }

    public function test_me_never_exposes_sensitive_columns(): void
    {
        $user = $this->makeClient();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonMissingPath('user.password')
            ->assertJsonMissingPath('user.remember_token')
            ->assertJsonMissingPath('user.is_super_admin');
    }
}
