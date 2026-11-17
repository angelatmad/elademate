<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $base = $user->tasks();

        $total = (clone $base)->count();
        $completed = (clone $base)->where('status', TaskStatus::Completed->value)->count();
        $inProgress = (clone $base)->where('status', TaskStatus::InProgress->value)->count();
        $pending = (clone $base)->where('status', TaskStatus::Pending->value)->count();

        $overdue = (clone $base)
            ->where('status', '!=', TaskStatus::Completed->value)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->count();

        $todayTasks = (clone $base)
            ->whereDate('due_date', today())
            ->orderByRaw('CASE WHEN status = ? THEN 1 ELSE 0 END', [TaskStatus::Completed->value])
            ->orderBy('due_time')
            ->limit(10)
            ->get();

        $overdueTasks = (clone $base)
            ->where('status', '!=', TaskStatus::Completed->value)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->orderBy('due_date')
            ->limit(4)
            ->get();

        $upcomingTasks = (clone $base)
            ->where('status', '!=', TaskStatus::Completed->value)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>', today())
            ->orderBy('due_date')
            ->orderBy('due_time')
            ->limit(8)
            ->get();

        $recentTasks = (clone $base)
            ->latest()
            ->limit(5)
            ->get();

        $categoryStats = (clone $base)
            ->whereNotNull('category')
            ->select('category')
            ->selectRaw('count(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category,
                'total' => (int) $row->total,
            ]);

        $completedThisWeek = (clone $base)
            ->where('status', TaskStatus::Completed->value)
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return response()->json([
            'data' => [
                'user' => new UserResource($user),
                'stats' => [
                    'total' => $total,
                    'completed' => $completed,
                    'in_progress' => $inProgress,
                    'pending' => $pending,
                    'overdue' => $overdue,
                    'progress' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
                    'completed_this_week' => $completedThisWeek,
                ],
                'category_stats' => $categoryStats,
                'today_tasks' => TaskResource::collection($todayTasks)->resolve($request),
                'overdue_tasks' => TaskResource::collection($overdueTasks)->resolve($request),
                'upcoming_tasks' => TaskResource::collection($upcomingTasks)->resolve($request),
                'recent_tasks' => TaskResource::collection($recentTasks)->resolve($request),
            ],
        ]);
    }

    public function notifications(Request $request): JsonResponse
    {
        $soon = today()->addDays(3)->toDateString();

        $tasks = $request->user()->tasks()
            ->where('status', '!=', TaskStatus::Completed->value)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', $soon)
            ->orderBy('due_date')
            ->orderBy('due_time')
            ->limit(6)
            ->get();

        return response()->json([
            'data' => TaskResource::collection($tasks)->resolve($request),
            'meta' => [
                'count' => $request->user()->tasks()
                    ->where('status', '!=', TaskStatus::Completed->value)
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<=', $soon)
                    ->count(),
            ],
        ]);
    }
}