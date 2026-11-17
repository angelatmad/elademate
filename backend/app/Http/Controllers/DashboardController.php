<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
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
            ->orderBy('due_time')
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
            ->limit(5)
            ->get();

        $recentTasks = (clone $base)
            ->latest()
            ->limit(5)
            ->get();

        $progress = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        $categoryStats = (clone $base)
            ->whereNotNull('category')
            ->select('category')
            ->selectRaw('count(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $completedThisWeek = (clone $base)
            ->where('status', TaskStatus::Completed->value)
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return view('dashboard', [
            'totalTasks' => $total,
            'completedTasks' => $completed,
            'inProgressTasks' => $inProgress,
            'pendingTasks' => $pending,
            'overdueTasks' => $overdue,
            'overdueTaskList' => $overdueTasks,
            'todayTasks' => $todayTasks,
            'upcomingTasks' => $upcomingTasks,
            'recentTasks' => $recentTasks,
            'progress' => $progress,
            'categoryStats' => $categoryStats,
            'completedThisWeek' => $completedThisWeek,
            'categories' => Task::CATEGORIES,
        ]);
    }
}
