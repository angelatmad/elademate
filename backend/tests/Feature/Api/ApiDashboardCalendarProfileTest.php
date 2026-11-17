<?php

namespace Tests\Feature\Api;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiDashboardCalendarProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_real_task_statistics(): void
    {
        $user = User::factory()->create();
        Task::factory()->for($user)->create(['status' => TaskStatus::Completed, 'category' => 'School']);
        Task::factory()->for($user)->create(['status' => TaskStatus::InProgress, 'category' => 'School']);
        Task::factory()->for($user)->create(['status' => TaskStatus::Pending, 'category' => 'Personal']);

        Sanctum::actingAs($user);

        $this->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonPath('data.stats.total', 3)
            ->assertJsonPath('data.stats.completed', 1)
            ->assertJsonPath('data.stats.in_progress', 1)
            ->assertJsonPath('data.stats.pending', 1)
            ->assertJsonPath('data.stats.progress', 33)
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'stats',
                    'category_stats',
                    'today_tasks',
                    'overdue_tasks',
                    'upcoming_tasks',
                    'recent_tasks',
                ],
            ]);
    }

    public function test_notifications_return_tasks_due_soon(): void
    {
        $user = User::factory()->create();
        Task::factory()->for($user)->create([
            'title' => 'Due tomorrow',
            'status' => TaskStatus::Pending,
            'due_date' => today()->addDay(),
        ]);
        Task::factory()->for($user)->create([
            'title' => 'Far away',
            'status' => TaskStatus::Pending,
            'due_date' => today()->addDays(20),
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonPath('meta.count', 1)
            ->assertJsonPath('data.0.title', 'Due tomorrow');
    }

    public function test_calendar_returns_month_grid_and_scheduled_tasks(): void
    {
        $user = User::factory()->create();
        $date = today()->startOfMonth()->addDays(2);
        Task::factory()->for($user)->create([
            'title' => 'Calendar task',
            'priority' => TaskPriority::Medium,
            'status' => TaskStatus::Pending,
            'due_date' => $date,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/calendar?month='.$date->month.'&year='.$date->year)
            ->assertOk()
            ->assertJsonPath('data.month', $date->month)
            ->assertJsonPath('data.year', $date->year)
            ->assertJsonPath('data.total_this_month', 1)
            ->assertJsonStructure(['data' => ['days', 'weekdays', 'previous', 'next']]);

        $day = collect($response->json('data.days'))->firstWhere('date', $date->toDateString());

        $this->assertNotNull($day);
        $this->assertSame('Calendar task', $day['tasks'][0]['title']);
    }

    public function test_user_can_update_profile_and_password_through_the_api(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'password' => 'password',
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/profile', [
            'name' => 'Alyssa Cruz',
            'email' => 'alyssa@example.com',
        ])
            ->assertOk()
            ->assertJsonPath('data.user.name', 'Alyssa Cruz')
            ->assertJsonPath('data.user.email', 'alyssa@example.com');

        $this->putJson('/api/profile/password', [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])
            ->assertOk();

        $this->assertTrue(password_verify('new-password-123', $user->fresh()->password));
    }

    public function test_profile_update_validates_unique_email(): void
    {
        $user = User::factory()->create();
        $existing = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson('/api/profile', [
            'name' => $user->name,
            'email' => $existing->email,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }
}
