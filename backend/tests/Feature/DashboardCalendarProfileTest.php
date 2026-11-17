<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardCalendarProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_real_task_statistics(): void
    {
        $user = User::factory()->create(['name' => 'Alyssa Cruz']);

        Task::factory()->for($user)->create([
            'title' => 'Dashboard task',
            'status' => TaskStatus::Completed,
            'due_date' => today(),
        ]);
        Task::factory()->for($user)->create(['status' => TaskStatus::Pending, 'due_date' => today()->addDay()]);
        Task::factory()->for($user)->create(['status' => TaskStatus::InProgress, 'due_date' => today()->addDays(2)]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard task')
            ->assertViewHas('totalTasks', 3)
            ->assertViewHas('completedTasks', 1)
            ->assertViewHas('inProgressTasks', 1)
            ->assertViewHas('pendingTasks', 1);
    }

    public function test_calendar_shows_tasks_for_the_selected_month(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create([
            'title' => 'Calendar visible task',
            'due_date' => now()->startOfMonth()->addDays(4)->toDateString(),
        ]);

        $this->actingAs($user)->get(route('calendar', [
            'month' => $task->due_date->month,
            'year' => $task->due_date->year,
            'date' => $task->due_date->toDateString(),
        ]))
            ->assertOk()
            ->assertSee('Calendar visible task')
            ->assertSee($task->due_date->format('F Y'));
    }

    public function test_calendar_navigation_links_work(): void
    {
        $user = User::factory()->create();
        $next = now()->addMonthNoOverflow();

        $this->actingAs($user)->get(route('calendar', [
            'month' => $next->month,
            'year' => $next->year,
        ]))
            ->assertOk()
            ->assertSee($next->format('F Y'));
    }

    public function test_user_can_update_profile_information(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ])->assertRedirect(route('settings'));

        $user->refresh();

        $this->assertSame('New Name', $user->name);
        $this->assertSame('new@example.com', $user->email);
    }

    public function test_profile_update_validates_unique_email(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($user)->from(route('settings'))->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $other->email,
        ])->assertRedirect(route('settings'))
            ->assertSessionHasErrors('email');
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);

        $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'old-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('settings'));

        $this->assertTrue(Hash::check('new-password-123', $user->refresh()->password));
    }

    public function test_change_password_requires_the_correct_current_password(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);

        $this->actingAs($user)->from(route('settings'))->put(route('profile.password'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('settings'))
            ->assertSessionHasErrors('current_password');
    }
}
