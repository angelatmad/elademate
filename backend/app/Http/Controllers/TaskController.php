<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $tasks)
    {
    }

    public function index(Request $request): View
    {
        $filters = [
            'q' => mb_substr(trim((string) $request->query('q', '')), 0, 100),
            'status' => in_array($request->query('status'), TaskStatus::values(), true) ? $request->query('status') : '',
            'priority' => in_array($request->query('priority'), TaskPriority::values(), true) ? $request->query('priority') : '',
            'category' => in_array($request->query('category'), Task::CATEGORIES, true) ? $request->query('category') : '',
            'sort' => in_array($request->query('sort'), ['due_date', 'due_date_desc', 'newest', 'oldest', 'title'], true)
                ? $request->query('sort')
                : 'due_date',
        ];

        return view('tasks.index', [
            'tasks' => $this->tasks->filteredTasks($request->user(), $filters),
            'filters' => $filters,
            'counts' => $this->tasks->statusCounts($request->user()),
            'categories' => Task::CATEGORIES,
            'priorities' => TaskPriority::options(),
            'statuses' => TaskStatus::options(),
        ]);
    }

    public function create(Request $request): View
    {
        $task = new Task([
            'priority' => TaskPriority::Medium,
            'status' => TaskStatus::Pending,
        ]);

        if ($request->filled('date') && strtotime((string) $request->query('date')) !== false) {
            $task->due_date = $request->query('date');
        }

        return view('tasks.create', [
            'task' => $task,
            'categories' => Task::CATEGORIES,
            'priorities' => TaskPriority::cases(),
            'statuses' => TaskStatus::cases(),
        ]);
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        $task = $this->tasks->create($request->user(), $request->validated());

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task "'.$task->title.'" was created successfully.');
    }

    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        return view('tasks.show', ['task' => $task]);
    }

    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        return view('tasks.edit', [
            'task' => $task,
            'categories' => Task::CATEGORIES,
            'priorities' => TaskPriority::cases(),
            'statuses' => TaskStatus::cases(),
        ]);
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $this->tasks->update($task, $request->validated());

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task "'.$task->title.'" was updated successfully.');
    }

    public function toggle(Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $this->tasks->toggleCompletion($task);

        $message = $task->isCompleted()
            ? 'Great job! "'.$task->title.'" is marked as completed.'
            : 'Task "'.$task->title.'" is active again.';

        return back()->with('success', $message);
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $title = $task->title;
        $this->tasks->delete($task);

        return redirect()->route('tasks.index')
            ->with('success', 'Task "'.$title.'" was deleted.');
    }
}
