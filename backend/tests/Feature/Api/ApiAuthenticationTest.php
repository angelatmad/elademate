<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_is_public(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok');
    }

    public function test_user_can_register_through_the_api(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Alyssa Cruz',
            'email' => 'alyssa@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'device_name' => 'phpunit',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.user.email', 'alyssa@example.com')
            ->assertJsonStructure(['data' => ['user', 'token', 'token_type']]);

        $this->assertDatabaseHas('users', ['email' => 'alyssa@example.com']);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'phpunit',
        ])
            ->assertOk()
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_invalid_credentials_return_validation_errors(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_api_routes_require_authentication(): void
    {
        $this->getJson('/api/tasks')->assertUnauthorized();
        $this->getJson('/api/dashboard')->assertUnauthorized();
        $this->getJson('/api/calendar')->assertUnauthorized();
        $this->getJson('/api/profile')->assertUnauthorized();
    }

    public function test_token_can_be_used_and_revoked_on_logout(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $token = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->json('data.token');

        $headers = ['Authorization' => 'Bearer '.$token];

        $this->withHeaders($headers)->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);

        $this->withHeaders($headers)->postJson('/api/logout')->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->app['auth']->forgetGuards();

        $this->withHeaders($headers)->getJson('/api/user')->assertUnauthorized();
    }
}
