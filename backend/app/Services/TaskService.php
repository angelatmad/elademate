<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    public function create(User $user, array $attributes): Task
    {
        return $user->tasks()->create($attributes);
    }

    public function update(Task $task, array $attributes): Task
    {
        $task->update($attributes);

        return $task->refresh();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    public function toggleCompletion(Task $task): Task
    {
        $task->status = $task->status === TaskStatus::Completed
            ? TaskStatus::Pending
            : TaskStatus::Completed;

        $task->save();

        return $task;
    }

    /**
     * @param  array{q?: string|null, status?: string|null, priority?: string|null, category?: string|null, sort?: string|null}  $filters
     */
    public function filteredTasks(User $user, array $filters, int $perPage = 8): LengthAwarePaginator
    {
        $sort = $filters['sort'] ?? 'due_date';
        $status = $filters['status'] ?? '';

        $query = $user->tasks()
            ->search($filters['q'] ?? null)
            ->priority($filters['priority'] ?? null)
            ->category($filters['category'] ?? null);

        if ($status === 'overdue') {
            $this->constrainOverdue($query);
        } else {
            $query->status($status);
        }

        match ($sort) {
            'newest' => $query->latest(),
            'oldest' => $query->oldest(),
            'title' => $query->orderBy('title'),
            'due_date_desc' => $query->orderByRaw('due_date IS NULL')->orderByDesc('due_date'),
            default => $query->orderByRaw('due_date IS NULL')->orderBy('due_date')->orderBy('due_time'),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @return array<string, int>
     */
    public function statusCounts(User $user): array
    {
        $counts = $user->tasks()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $overdueQuery = $user->tasks();
        $this->constrainOverdue($overdueQuery);

        return [
            'all' => array_sum($counts),
            'pending' => (int) ($counts[TaskStatus::Pending->value] ?? 0),
            'in_progress' => (int) ($counts[TaskStatus::InProgress->value] ?? 0),
            'completed' => (int) ($counts[TaskStatus::Completed->value] ?? 0),
            'overdue' => $overdueQuery->count(),
        ];
    }

    /**
     * Constrain a query to tasks that are past their due date and not completed.
     */
    private function constrainOverdue($query): void
    {
        $query->where('status', '!=', TaskStatus::Completed->value)
            ->whereNotNull('due_date')
            ->where(function ($query) {
                $query->whereDate('due_date', '<', today())
                    ->orWhere(function ($query) {
                        $query->whereDate('due_date', today())
                            ->whereNotNull('due_time')
                            ->whereTime('due_time', '<', now()->format('H:i:s'));
                    });
            });
    }
}
