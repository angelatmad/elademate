<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $selectedDate = today();

        if ($request->filled('date')) {
            try {
                $selectedDate = Carbon::createFromFormat('Y-m-d', (string) $request->query('date'))->startOfDay();
            } catch (\Throwable) {
                $selectedDate = today();
            }
        }

        $month = min(12, max(1, (int) $request->query('month', $selectedDate->month)));
        $year = min(2100, max(2000, (int) $request->query('year', $selectedDate->year)));

        $currentMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $gridStart = $currentMonth->copy()->startOfWeek(Carbon::SUNDAY);
        $gridEnd = $currentMonth->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $tasks = $request->user()->tasks()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->orderBy('due_time')
            ->get()
            ->groupBy(fn ($task) => $task->due_date->toDateString());

        $days = [];
        for ($day = $gridStart->copy(); $day->lte($gridEnd); $day->addDay()) {
            $date = $day->toDateString();
            $days[] = [
                'date' => $date,
                'day' => $day->day,
                'in_month' => $day->month === $currentMonth->month,
                'is_today' => $day->isToday(),
                'is_selected' => $day->isSameDay($selectedDate),
                'tasks' => TaskResource::collection($tasks->get($date, collect()))->resolve($request),
            ];
        }

        $prev = $currentMonth->copy()->subMonthNoOverflow();
        $next = $currentMonth->copy()->addMonthNoOverflow();

        return response()->json([
            'data' => [
                'month' => $currentMonth->month,
                'year' => $currentMonth->year,
                'month_label' => $currentMonth->format('F Y'),
                'today' => today()->toDateString(),
                'selected_date' => $selectedDate->toDateString(),
                'weekdays' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                'days' => $days,
                'total_this_month' => $request->user()->tasks()
                    ->whereBetween('due_date', [$currentMonth->toDateString(), $currentMonth->copy()->endOfMonth()->toDateString()])
                    ->count(),
                'previous' => ['month' => $prev->month, 'year' => $prev->year, 'label' => $prev->format('F Y')],
                'next' => ['month' => $next->month, 'year' => $next->year, 'label' => $next->format('F Y')],
            ],
        ]);
    }
}
