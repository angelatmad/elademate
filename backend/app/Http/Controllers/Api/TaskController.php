<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $tasks)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $this->filters($request);
        $perPage = min(50, max(1, (int) $request->query('per_page', 8)));

        $tasks = $this->tasks->filteredTasks($request->user(), $filters, $perPage);

        return response()->json([
            'data' => $tasks->getCollection()
                ->map(fn (Task $task) => (new TaskResource($task))->resolve($request))
                ->values(),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
                'from' => $tasks->firstItem(),
                'to' => $tasks->lastItem(),
            ],
            'filters' => $filters,
            'counts' => $this->tasks->statusCounts($request->user()),
            'options' => [
                'categories' => Task::CATEGORIES,
                'priorities' => TaskPriority::options(),
                'statuses' => TaskStatus::options(),
            ],
        ]);
    }

    public function store(TaskRequest $request): JsonResponse
    {
        $task = $this->tasks->create($request->user(), $request->validated());

        return response()->json([
            'message' => 'Task "'.$task->title.'" was created successfully.',
            'data' => new TaskResource($task),
        ], 201);
    }

    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        return response()->json([
            'data' => new TaskResource($task),
        ]);
    }

    public function update(TaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task = $this->tasks->update($task, $request->validated());

        return response()->json([
            'message' => 'Task "'.$task->title.'" was updated successfully.',
            'data' => new TaskResource($task),
        ]);
    }

    public function toggle(Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task = $this->tasks->toggleCompletion($task);

        return response()->json([
            'message' => $task->isCompleted()
                ? 'Great job! "'.$task->title.'" is marked as completed.'
                : 'Task "'.$task->title.'" is active again.',
            'data' => new TaskResource($task),
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $title = $task->title;
        $this->tasks->delete($task);

        return response()->json([
            'message' => 'Task "'.$title.'" was deleted.',
        ]);
    }

    /**
     * @return array{q: string, status: string, priority: string, category: string, sort: string}
     */
    private function filters(Request $request): array
    {
        return [
            'q' => mb_substr(trim((string) $request->query('q', '')), 0, 100),
            'status' => in_array($request->query('status'), [...TaskStatus::values(), 'overdue'], true) ? $request->query('status') : '',
            'priority' => in_array($request->query('priority'), TaskPriority::values(), true) ? $request->query('priority') : '',
            'category' => in_array($request->query('category'), Task::CATEGORIES, true) ? $request->query('category') : '',
            'sort' => in_array($request->query('sort'), ['due_date', 'due_date_desc', 'newest', 'oldest', 'title'], true)
                ? $request->query('sort')
                : 'due_date',
        ];
    }
}
