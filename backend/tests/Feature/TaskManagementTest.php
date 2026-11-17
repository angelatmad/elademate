<?php

namespace Tests\Feature;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_task(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Finish HCI final project',
            'description' => 'Complete the documentation and prototype.',
            'category' => 'School',
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => now()->addDay()->toDateString(),
            'due_time' => '17:00',
        ])->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Finish HCI final project',
            'priority' => 'high',
            'status' => 'pending',
        ]);
    }

    public function test_task_creation_is_validated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from(route('tasks.create'))->post(route('tasks.store'), [
            'title' => '',
            'priority' => 'urgent',
            'status' => 'unknown',
        ])->assertRedirect(route('tasks.create'))
            ->assertSessionHasErrors(['title', 'priority', 'status']);
    }

    public function test_due_time_requires_a_due_date(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from(route('tasks.create'))->post(route('tasks.store'), [
            'title' => 'Task with time only',
            'priority' => 'medium',
            'status' => 'pending',
            'due_time' => '10:00',
        ])->assertSessionHasErrors('due_date');
    }

    public function test_user_can_update_their_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create([
            'title' => 'Old title',
            'priority' => TaskPriority::Low,
            'status' => TaskStatus::Pending,
        ]);

        $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Updated title',
            'description' => 'Updated description',
            'category' => 'Personal',
            'priority' => 'high',
            'status' => 'in_progress',
            'due_date' => now()->addDays(2)->toDateString(),
            'due_time' => '09:30',
        ])->assertRedirect(route('tasks.show', $task));

        $task->refresh();

        $this->assertSame('Updated title', $task->title);
        $this->assertSame(TaskPriority::High, $task->priority);
        $this->assertSame(TaskStatus::InProgress, $task->status);
    }

    public function test_user_can_toggle_task_completion(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create(['status' => TaskStatus::Pending]);

        $this->actingAs($user)
            ->patch(route('tasks.toggle', $task))
            ->assertRedirect();

        $this->assertSame(TaskStatus::Completed, $task->refresh()->status);

        $this->actingAs($user)
            ->patch(route('tasks.toggle', $task))
            ->assertRedirect();

        $this->assertSame(TaskStatus::Pending, $task->refresh()->status);
    }

    public function test_user_can_delete_their_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete(route('tasks.destroy', $task))
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_task_list_only_shows_tasks_owned_by_the_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Task::factory()->for($user)->create(['title' => 'My visible task']);
        Task::factory()->for($other)->create(['title' => 'Someone else task']);

        $this->actingAs($user)->get(route('tasks.index'))
            ->assertOk()
            ->assertSee('My visible task')
            ->assertDontSee('Someone else task');
    }

    public function test_user_cannot_view_update_or_delete_another_users_task(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::factory()->for($other)->create();

        $this->actingAs($user)->get(route('tasks.show', $task))->assertForbidden();
        $this->actingAs($user)->get(route('tasks.edit', $task))->assertForbidden();
        $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Hijacked',
            'priority' => 'high',
            'status' => 'pending',
        ])->assertForbidden();
        $this->actingAs($user)->delete(route('tasks.destroy', $task))->assertForbidden();
        $this->actingAs($user)->patch(route('tasks.toggle', $task))->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => $task->title]);
    }

    public function test_tasks_can_be_searched_and_filtered(): void
    {
        $user = User::factory()->create();

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

        $this->actingAs($user)->get(route('tasks.index', ['q' => 'Math']))
            ->assertOk()
            ->assertSee('Math assignment')
            ->assertDontSee('Buy groceries');

        $this->actingAs($user)->get(route('tasks.index', ['priority' => 'low']))
            ->assertOk()
            ->assertSee('Buy groceries')
            ->assertDontSee('Math assignment');

        $this->actingAs($user)->get(route('tasks.index', ['category' => 'School']))
            ->assertOk()
            ->assertSee('Math assignment')
            ->assertDontSee('Buy groceries');
    }
}

