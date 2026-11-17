<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'category' => fake()->randomElement(Task::CATEGORIES),
            'priority' => fake()->randomElement(TaskPriority::values()),
            'status' => fake()->randomElement(TaskStatus::values()),
            'due_date' => fake()->dateTimeBetween('-5 days', '+14 days')->format('Y-m-d'),
            'due_time' => fake()->randomElement(['08:00', '10:30', '13:00', '16:45', '19:00']),
        ];
    }
}
