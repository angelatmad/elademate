<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __invoke(Request $request): View
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

        $days = collect();
        for ($day = $gridStart->copy(); $day->lte($gridEnd); $day->addDay()) {
            $days->push([
                'date' => $day->copy(),
                'inMonth' => $day->month === $currentMonth->month,
                'isToday' => $day->isToday(),
                'isSelected' => $day->isSameDay($selectedDate),
                'tasks' => $tasks->get($day->toDateString(), collect()),
            ]);
        }

        $weeks = $days->chunk(7);

        return view('calendar.index', [
            'currentMonth' => $currentMonth,
            'weeks' => $weeks,
            'selectedDate' => $selectedDate,
            'selectedTasks' => $tasks->get($selectedDate->toDateString(), collect()),
            'totalThisMonth' => $request->user()->tasks()
                ->whereBetween('due_date', [$currentMonth->toDateString(), $currentMonth->copy()->endOfMonth()->toDateString()])
                ->count(),
            'prevLink' => route('calendar', [
                'month' => $currentMonth->copy()->subMonthNoOverflow()->month,
                'year' => $currentMonth->copy()->subMonthNoOverflow()->year,
                'date' => $currentMonth->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(),
            ]),
            'nextLink' => route('calendar', [
                'month' => $currentMonth->copy()->addMonthNoOverflow()->month,
                'year' => $currentMonth->copy()->addMonthNoOverflow()->year,
                'date' => $currentMonth->copy()->addMonthNoOverflow()->startOfMonth()->toDateString(),
            ]),
            'todayLink' => route('calendar', ['date' => today()->toDateString()]),
        ]);
    }
}
