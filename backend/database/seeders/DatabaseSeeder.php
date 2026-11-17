<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::updateOrCreate(
            ['email' => 'student@elademate.com'],
            [
                'name' => 'Alyssa Cruz',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'elade@gmail.com'],
            [
                'name' => 'Elade User',
                'password' => '12345678',
                'email_verified_at' => now(),
            ]
        );

        $student->tasks()->delete();

        $tasks = [
            [
                'title' => 'Finish HCI final project paper',
                'description' => 'Complete the documentation, wireframes, and usability testing section.',
                'category' => 'School',
                'priority' => TaskPriority::High,
                'status' => TaskStatus::InProgress,
                'due_date' => today()->toDateString(),
                'due_time' => '17:00',
            ],
            [
                'title' => 'Review OOP 2 notes',
                'description' => 'Review MVC, dependency injection, and Laravel Eloquent relationships.',
                'category' => 'School',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::Pending,
                'due_date' => today()->toDateString(),
                'due_time' => '20:00',
            ],
            [
                'title' => 'Submit scholarship requirements',
                'description' => 'Upload the certificate of enrollment and latest grade report.',
                'category' => 'Personal',
                'priority' => TaskPriority::High,
                'status' => TaskStatus::Pending,
                'due_date' => today()->addDays(2)->toDateString(),
                'due_time' => '09:00',
            ],
            [
                'title' => 'Org meeting with creatives team',
                'description' => 'Prepare the social media calendar for the upcoming foundation week.',
                'category' => 'Organization',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::Pending,
                'due_date' => today()->addDays(3)->toDateString(),
                'due_time' => '15:30',
            ],
            [
                'title' => 'Finish database normalization exercise',
                'description' => 'Normalize the sample schema up to the third normal form.',
                'category' => 'School',
                'priority' => TaskPriority::High,
                'status' => TaskStatus::Completed,
                'due_date' => today()->subDay()->toDateString(),
                'due_time' => '14:00',
            ],
            [
                'title' => 'Buy vitamins and refill water',
                'description' => 'Drop by the pharmacy after class.',
                'category' => 'Health',
                'priority' => TaskPriority::Low,
                'status' => TaskStatus::Pending,
                'due_date' => today()->addDays(4)->toDateString(),
                'due_time' => '18:00',
            ],
            [
                'title' => 'Pass the PE dance routine',
                'description' => 'Practice the full choreography with the group.',
                'category' => 'School',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::Completed,
                'due_date' => today()->subDays(3)->toDateString(),
                'due_time' => '10:00',
            ],
            [
                'title' => 'Plan weekend family lunch',
                'description' => 'Reserve a table and send the invitation to everyone.',
                'category' => 'Personal',
                'priority' => TaskPriority::Low,
                'status' => TaskStatus::Pending,
                'due_date' => today()->addDays(6)->toDateString(),
                'due_time' => '11:00',
            ],
            [
                'title' => 'Update org membership database',
                'description' => 'Encode the new member applications and verify student numbers.',
                'category' => 'Organization',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::InProgress,
                'due_date' => today()->addDays(1)->toDateString(),
                'due_time' => '13:00',
            ],
            [
                'title' => 'Prepare presentation slides for OOP 2',
                'description' => 'Explain the MVC flow, Eloquent models, and authentication in the demo.',
                'category' => 'School',
                'priority' => TaskPriority::High,
                'status' => TaskStatus::Pending,
                'due_date' => today()->addDays(5)->toDateString(),
                'due_time' => '08:30',
            ],
            [
                'title' => 'Return borrowed library books',
                'description' => 'Books are due at the main library counter.',
                'category' => 'School',
                'priority' => TaskPriority::Medium,
                'status' => TaskStatus::Pending,
                'due_date' => today()->subDays(2)->toDateString(),
                'due_time' => '16:00',
            ],
            [
                'title' => 'Journal and weekly reflection',
                'description' => 'Write down three wins and one thing to improve next week.',
                'category' => 'Personal',
                'priority' => TaskPriority::Low,
                'status' => TaskStatus::Completed,
                'due_date' => today()->subDays(4)->toDateString(),
                'due_time' => '21:00',
            ],
        ];

        foreach ($tasks as $task) {
            Task::create(['user_id' => $student->id, ...$task]);
        }
    }
}
