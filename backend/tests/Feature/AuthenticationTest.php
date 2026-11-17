<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible_to_guests(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Log in to EladeMate', false);
    }

    public function test_user_can_log_in_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'student@example.com',
            'password' => 'password',
        ]);

        $this->post(route('login.store'), [
            'email' => 'student@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_log_in_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'student@example.com',
            'password' => 'password',
        ]);

        $this->from(route('login'))->post(route('login.store'), [
            'email' => 'student@example.com',
            'password' => 'wrong-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_register_and_is_logged_in(): void
    {
        $this->post(route('register.store'), [
            'name' => 'New Student',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
        $this->assertAuthenticated();
    }

    public function test_registration_requires_matching_password_confirmation(): void
    {
        $this->from(route('register'))->post(route('register.store'), [
            'name' => 'New Student',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ])->assertRedirect(route('register'))
            ->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_guests_are_redirected_from_protected_pages(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('tasks.index'))->assertRedirect(route('login'));
        $this->get(route('calendar'))->assertRedirect(route('login'));
        $this->get(route('settings'))->assertRedirect(route('login'));
    }
}
