<?php

namespace Tests\Feature\Api;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_manage_tasks_through_the_api(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $create = $this->postJson('/api/tasks', [
            'title' => 'Finish HCI paper',
            'description' => 'Complete the final chapter and references.',
            'category' => 'School',
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => today()->addDays(2)->toDateString(),
            'due_time' => '17:00',
        ]);

        $create->assertCreated()
            ->assertJsonPath('data.title', 'Finish HCI paper')
            ->assertJsonPath('data.priority', 'high');

        $taskId = $create->json('data.id');
        $this->assertDatabaseHas('tasks', ['id' => $taskId, 'user_id' => $user->id]);

        $this->getJson("/api/tasks/{$taskId}")
            ->assertOk()
            ->assertJsonPath('data.title', 'Finish HCI paper');

        $this->putJson("/api/tasks/{$taskId}", [
            'title' => 'Finish HCI final paper',
            'description' => 'Complete the final chapter and references.',
            'category' => 'School',
            'priority' => 'medium',
            'status' => 'in_progress',
            'due_date' => today()->addDays(3)->toDateString(),
            'due_time' => '18:30',
        ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Finish HCI final paper')
            ->assertJsonPath('data.status', 'in_progress');

        $this->patchJson("/api/tasks/{$taskId}/toggle")
            ->assertOk()
            ->assertJsonPath('data.status', 'completed');

        $this->patchJson("/api/tasks/{$taskId}/toggle")
            ->assertOk()
            ->assertJsonPath('data.status', 'pending');

        $this->deleteJson("/api/tasks/{$taskId}")->assertOk();

        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }

    public function test_task_creation_is_validated(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/tasks', [
            'title' => '',
            'priority' => 'urgent',
            'status' => 'unknown',
            'due_time' => '17:00',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'priority', 'status', 'due_date']);
    }

    public function test_users_cannot_access_another_users_task(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        Sanctum::actingAs($other);

        $this->getJson("/api/tasks/{$task->id}")->assertForbidden();
        $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Changed',
            'priority' => 'low',
            'status' => 'pending',
        ])->assertForbidden();
        $this->patchJson("/api/tasks/{$task->id}/toggle")->assertForbidden();
        $this->deleteJson("/api/tasks/{$task->id}")->assertForbidden();
    }

    public function test_task_list_supports_search_filters_and_pagination_metadata(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Task::factory()->for($user)->create([
            'title' => 'Math assignment',
            'category' => 'School',
            'priority' => TaskPriority::High,
            'status' => TaskStatus::Pending,
            'due_date' => today()->addDays(10),
        ]);

        Task::factory()->for($user)->create([
            'title' => 'Buy groceries',
            'category' => 'Personal',
            'priority' => TaskPriority::Low,
            'status' => TaskStatus::Completed,
            'due_date' => today()->addDays(10),
        ]);

        Task::factory()->for(User::factory())->create(['title' => 'Another user task']);

        $this->getJson('/api/tasks?q=Math&priority=high&category=School&status=pending')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Math assignment')
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('counts.all', 2)
            ->assertJsonPath('counts.pending', 1)
            ->assertJsonPath('counts.completed', 1);
    }

    public function test_task_list_can_filter_overdue_tasks(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Task::factory()->for($user)->create([
            'title' => 'Late assignment',
            'status' => TaskStatus::Pending,
            'due_date' => today()->subDay(),
            'due_time' => '09:00',
        ]);

        Task::factory()->for($user)->create([
            'title' => 'Already finished late task',
            'status' => TaskStatus::Completed,
            'due_date' => today()->subDay(),
            'due_time' => '09:00',
        ]);

        Task::factory()->for($user)->create([
            'title' => 'Future assignment',
            'status' => TaskStatus::Pending,
            'due_date' => today()->addDays(5),
            'due_time' => '09:00',
        ]);

        $this->getJson('/api/tasks?status=overdue')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Late assignment')
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('counts.overdue', 1);
    }

    public function test_task_list_returns_the_current_users_tasks_only(): void
    {
        $user = User::factory()->create();
        Task::factory()->for($user)->count(3)->create();
        Task::factory()->for(User::factory())->count(2)->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/tasks?per_page=50')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.total', 3);
    }
}
