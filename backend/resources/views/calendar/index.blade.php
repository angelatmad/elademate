@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
@php
    $weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
@endphp

<div class="space-y-5">
    <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-medium text-ink-500">Plan ahead</p>
            <h1 class="page-title mt-1">Calendar</h1>
            <p class="mt-1 text-sm text-ink-500">See your month at a glance and give every deadline a home.</p>
        </div>
        <a href="{{ route('tasks.create', ['date' => $selectedDate->toDateString()]) }}" class="btn-primary shrink-0">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Add Task
        </a>
    </section>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_360px]">
        <section class="card overflow-hidden" aria-labelledby="calendar-month">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-brand-50 p-4 sm:p-5">
                <div>
                    <h2 id="calendar-month" class="text-lg font-bold text-ink-900">{{ $currentMonth->format('F Y') }}</h2>
                    <p class="text-[11px] text-ink-500">{{ $totalThisMonth }} {{ Str::plural('task', $totalThisMonth) }} scheduled this month</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <a href="{{ $prevLink }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-brand-100 text-ink-500 transition hover:bg-brand-50 hover:text-brand-700" aria-label="Previous month">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </a>
                    <a href="{{ $todayLink }}" class="rounded-lg border border-brand-100 px-3 py-2 text-xs font-semibold text-ink-700 transition hover:bg-brand-50 hover:text-brand-700">Today</a>
                    <a href="{{ $nextLink }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-brand-100 text-ink-500 transition hover:bg-brand-50 hover:text-brand-700" aria-label="Next month">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                </div>
            </div>

            <div class="p-3 sm:p-5">
                <div class="grid grid-cols-7 gap-1 sm:gap-2" role="grid" aria-label="{{ $currentMonth->format('F Y') }} calendar">
                    @foreach ($weekdays as $weekday)
                        <div class="py-1.5 text-center text-[10px] font-semibold tracking-wide text-ink-500 uppercase sm:text-[11px]" role="columnheader">{{ $weekday }}</div>
                    @endforeach

                    @foreach ($weeks as $week)
                        @foreach ($week as $day)
                            @php
                                $isSelected = $day['isSelected'];
                                $dayTasks = $day['tasks'];
                                $dayLink = route('calendar', [
                                    'month' => $day['date']->month,
                                    'year' => $day['date']->year,
                                    'date' => $day['date']->toDateString(),
                                ]);
                            @endphp
                            <a href="{{ $dayLink }}"
                               role="gridcell"
                               aria-label="{{ $day['date']->format('F j, Y') }}, {{ $dayTasks->count() }} {{ Str::plural('task', $dayTasks->count()) }}"
                               aria-current="{{ $isSelected ? 'date' : '' }}"
                               class="group relative flex min-h-[72px] flex-col rounded-xl border p-1.5 transition sm:min-h-[100px] sm:p-2.5
                                      {{ $isSelected ? 'border-brand-400 bg-brand-50 ring-2 ring-brand-100' : 'border-transparent hover:border-brand-100 hover:bg-brand-50/50' }}
                                      {{ $day['inMonth'] ? '' : 'opacity-45' }}">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full text-[11px] font-semibold sm:h-7 sm:w-7 sm:text-xs
                                             {{ $day['isToday'] ? 'bg-brand-500 text-white shadow-sm shadow-brand-500/30' : ($isSelected ? 'text-brand-700' : 'text-ink-700') }}">
                                    {{ $day['date']->day }}
                                </span>

                                @if ($dayTasks->isNotEmpty())
                                    <span class="mt-auto flex flex-wrap items-center gap-0.5 pt-1 sm:hidden">
                                        @foreach ($dayTasks->take(3) as $task)
                                            <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $task->priority->dotColor() }}"></span>
                                        @endforeach
                                        @if ($dayTasks->count() > 3)
                                            <span class="text-[8px] font-bold text-ink-500">+{{ $dayTasks->count() - 3 }}</span>
                                        @endif
                                    </span>

                                    <span class="mt-1 hidden min-w-0 space-y-0.5 sm:block">
                                        @foreach ($dayTasks->take(2) as $task)
                                            <span class="flex items-center gap-1 truncate rounded-md bg-white/85 px-1.5 py-0.5 text-[9px] font-medium {{ $task->isCompleted() ? 'text-ink-500 line-through' : 'text-ink-700' }}">
                                                <span class="h-1 w-1 shrink-0 rounded-full" style="background-color: {{ $task->priority->dotColor() }}"></span>
                                                <span class="truncate">{{ $task->title }}</span>
                                            </span>
                                        @endforeach
                                        @if ($dayTasks->count() > 2)
                                            <span class="block pl-1 text-[9px] font-semibold text-brand-600">+{{ $dayTasks->count() - 2 }} more</span>
                                        @endif
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    @endforeach
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-brand-50 pt-3 text-[10px] text-ink-500">
                    <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-brand-500"></span> High priority</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-amber-500"></span> Medium priority</span>
                    <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Low priority</span>
                </div>
            </div>
        </section>

        <aside class="space-y-5">
            <section class="card p-5" aria-labelledby="selected-day-heading">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[10px] font-semibold tracking-[0.14em] text-brand-600 uppercase">
                            {{ $selectedDate->isToday() ? 'Today' : $selectedDate->format('l') }}
                        </p>
                        <h2 id="selected-day-heading" class="mt-1 text-lg font-bold text-ink-900">{{ $selectedDate->format('F j, Y') }}</h2>
                        <p class="text-[11px] text-ink-500">{{ $selectedTasks->count() }} {{ Str::plural('task', $selectedTasks->count()) }} scheduled</p>
                    </div>
                    <a href="{{ route('tasks.create', ['date' => $selectedDate->toDateString()]) }}" class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500 text-white shadow-md shadow-brand-500/25 transition hover:bg-brand-600" title="Add task on this day" aria-label="Add a task on {{ $selectedDate->format('F j, Y') }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    </a>
                </div>

                <div class="mt-4 space-y-2.5">
                    @forelse ($selectedTasks as $task)
                        <a href="{{ route('tasks.show', $task) }}" class="group block rounded-xl border border-brand-100 p-3 transition hover:border-brand-200 hover:bg-brand-50/50">
                            <div class="flex items-start gap-2.5">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full" style="background-color: {{ $task->priority->dotColor() }}"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-semibold {{ $task->isCompleted() ? 'text-ink-500 line-through' : 'text-ink-900' }} group-hover:text-brand-700">{{ $task->title }}</p>
                                    <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                        @if ($task->formattedDueTime())
                                            <span class="text-[10px] text-ink-500">{{ $task->formattedDueTime() }}</span>
                                        @endif
                                        @if ($task->category)
                                            <span class="text-[10px] text-ink-500">· {{ $task->category }}</span>
                                        @endif
                                        <x-status-badge :status="$task->status" />
                                    </div>
                                </div>
                                <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-brand-300 opacity-0 transition group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-xl border border-dashed border-brand-200 bg-brand-50/40 px-4 py-8 text-center">
                            <span class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-white text-brand-400 shadow-sm">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
                            </span>
                            <p class="mt-2 text-xs font-semibold text-ink-900">A free day</p>
                            <p class="mt-0.5 text-[11px] text-ink-500">Nothing scheduled. Add a task if you need to.</p>
                            <a href="{{ route('tasks.create', ['date' => $selectedDate->toDateString()]) }}" class="btn-secondary mt-3 !px-3 !py-1.5 text-[11px]">Add task</a>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-brand-100 bg-gradient-to-br from-brand-50 to-white p-5">
                <div class="flex items-center gap-2 text-brand-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/><circle cx="12" cy="12" r="3"/></svg>
                    <p class="text-xs font-semibold">Calendar tip</p>
                </div>
                <p class="mt-2 text-xs leading-relaxed text-ink-500">Click any date to view its tasks. The small colored dots show each task's priority, so you can spot busy days quickly.</p>
            </section>

            <a href="{{ route('tasks.index', ['sort' => 'due_date']) }}" class="flex items-center justify-between gap-3 rounded-2xl border border-brand-100 bg-white p-4 text-xs font-semibold text-ink-700 transition hover:border-brand-200 hover:bg-brand-50">
                View all tasks by due date
                <svg class="h-4 w-4 text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </aside>
    </div>
</div>
@endsection
