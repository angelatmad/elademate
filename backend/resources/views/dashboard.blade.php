@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();
    $maxCategory = $categoryStats->max('total') ?: 1;
    $categoryColors = ['from-brand-500 to-brand-700', 'from-sky-400 to-sky-600', 'from-amber-400 to-amber-600', 'from-emerald-400 to-emerald-600', 'from-violet-400 to-violet-600'];
@endphp

<div class="space-y-5">
    <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" aria-labelledby="dashboard-heading">
        <div>
            <p class="flex items-center gap-1.5 text-xs font-medium text-ink-500">
                <svg class="h-3.5 w-3.5 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
                {{ now()->translatedFormat('l, F j, Y') }}
            </p>
            <h1 id="dashboard-heading" class="page-title mt-1.5">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ $user->first_name }}</h1>
            <p class="mt-1 text-sm text-ink-500">
                @if ($todayTasks->count() > 0)
                    You have <span class="font-semibold text-brand-700">{{ $todayTasks->count() }} {{ Str::plural('task', $todayTasks->count()) }}</span> on your plate today. Let's make them count.
                @else
                    Nothing due today. A perfect moment to get ahead.
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('calendar') }}" class="btn-secondary">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
                Calendar
            </a>
            <a href="{{ route('tasks.create') }}" class="btn-primary">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Add Task
            </a>
        </div>
    </section>

    <section class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4" aria-label="Task statistics">
        <div class="card card-hover p-4 sm:p-5">
            <div class="flex items-start justify-between">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6h11M9 12h11M9 18h11"/><path d="m3 6 1.5 1.5L7 5M3 12l1.5 1.5L7 11M3 18l1.5 1.5L7 17"/></svg>
                </span>
                <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-semibold text-brand-700">All</span>
            </div>
            <p class="mt-3 text-2xl font-bold text-ink-900 sm:text-[28px]">{{ $totalTasks }}</p>
            <p class="text-xs font-medium text-ink-500">Total tasks</p>
        </div>

        <div class="card card-hover p-4 sm:p-5">
            <div class="flex items-start justify-between">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.3 2.3 4.7-5.3"/></svg>
                </span>
                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">Done</span>
            </div>
            <p class="mt-3 text-2xl font-bold text-ink-900 sm:text-[28px]">{{ $completedTasks }}</p>
            <p class="text-xs font-medium text-ink-500">Completed</p>
        </div>

        <div class="card card-hover p-4 sm:p-5">
            <div class="flex items-start justify-between">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                </span>
                <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[10px] font-semibold text-sky-700">Active</span>
            </div>
            <p class="mt-3 text-2xl font-bold text-ink-900 sm:text-[28px]">{{ $inProgressTasks }}</p>
            <p class="text-xs font-medium text-ink-500">In progress</p>
        </div>

        <div class="card card-hover p-4 sm:p-5">
            <div class="flex items-start justify-between">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18.4A2 2 0 0 0 3.5 21h17a2 2 0 0 0 1.7-2.6L13.7 3.9a2 2 0 0 0-3.4 0Z"/></svg>
                </span>
                <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-700">{{ $pendingTasks }} pending</span>
            </div>
            <p class="mt-3 text-2xl font-bold text-ink-900 sm:text-[28px]">{{ $overdueTasks }}</p>
            <p class="text-xs font-medium text-ink-500">Overdue</p>
        </div>
    </section>

    @if ($overdueTaskList->isNotEmpty())
        <section class="rounded-2xl border border-rose-200 bg-rose-50/70 p-4 sm:p-5" aria-labelledby="overdue-heading">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-rose-600 shadow-sm">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18.4A2 2 0 0 0 3.5 21h17a2 2 0 0 0 1.7-2.6L13.7 3.9a2 2 0 0 0-3.4 0Z"/></svg>
                    </span>
                    <h2 id="overdue-heading" class="text-sm font-semibold text-rose-900">Needs a little catch-up</h2>
                </div>
                <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="text-xs font-semibold text-rose-700 hover:text-rose-800">Review tasks →</a>
            </div>
            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                @foreach ($overdueTaskList as $task)
                    <a href="{{ route('tasks.show', $task) }}" class="flex items-center justify-between gap-3 rounded-xl bg-white px-3.5 py-2.5 shadow-sm transition hover:-translate-y-0.5">
                        <span class="min-w-0">
                            <span class="block truncate text-xs font-semibold text-ink-900">{{ $task->title }}</span>
                            <span class="text-[11px] text-rose-600">Was due {{ $task->formattedDueDate() }}</span>
                        </span>
                        <svg class="h-4 w-4 shrink-0 text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <div class="grid gap-5 xl:grid-cols-3">
        <section class="card p-5 sm:p-6 xl:col-span-2" aria-labelledby="today-heading">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 id="today-heading" class="section-title flex items-center gap-2">
                        Today's tasks
                        <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-semibold text-brand-700">{{ $todayTasks->count() }}</span>
                    </h2>
                    <p class="mt-0.5 text-xs text-ink-500">{{ now()->format('F j, Y') }} · {{ $todayTasks->where('status', \App\Enums\TaskStatus::Completed)->count() }} of {{ $todayTasks->count() }} done</p>
                </div>
                <a href="{{ route('tasks.create', ['date' => today()->toDateString()]) }}" class="btn-secondary !px-3 !py-2 text-xs">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Add
                </a>
            </div>

            <div class="mt-3 divide-y divide-brand-50">
                @forelse ($todayTasks as $task)
                    <x-task-row :task="$task" />
                @empty
                    <x-empty-state title="No tasks due today" message="Enjoy a little breathing room — or get a head start on upcoming deadlines." action-label="Add a task" :action-href="route('tasks.create', ['date' => today()->toDateString()])" />
                @endforelse
            </div>
        </section>

        <section class="card relative overflow-hidden p-5 sm:p-6" aria-labelledby="progress-heading">
            <div class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full bg-brand-50"></div>
            <h2 id="progress-heading" class="section-title relative">Task progress</h2>
            <p class="relative mt-0.5 text-xs text-ink-500">A gentle look at how far you've come.</p>

            <div class="relative mt-6 flex items-center justify-center">
                <div class="relative flex h-40 w-40 items-center justify-center rounded-full" style="background: conic-gradient(#e91e63 {{ $progress }}%, #f8dce8 {{ $progress }}% 100%);" role="img" aria-label="{{ $progress }} percent of tasks completed">
                    <div class="flex h-[122px] w-[122px] flex-col items-center justify-center rounded-full bg-white shadow-inner">
                        <span class="text-3xl font-bold text-ink-900">{{ $progress }}%</span>
                        <span class="text-[10px] font-semibold tracking-wide text-ink-500 uppercase">Completed</span>
                    </div>
                </div>
            </div>

            <div class="relative mt-6 grid grid-cols-2 gap-2">
                <div class="rounded-xl bg-brand-50/70 p-3 text-center">
                    <p class="text-lg font-bold text-brand-700">{{ $completedTasks }}</p>
                    <p class="text-[10px] font-medium text-ink-500">Tasks done</p>
                </div>
                <div class="rounded-xl bg-emerald-50 p-3 text-center">
                    <p class="text-lg font-bold text-emerald-700">{{ $completedThisWeek }}</p>
                    <p class="text-[10px] font-medium text-ink-500">Done this week</p>
                </div>
            </div>

            @if ($totalTasks === 0)
                <a href="{{ route('tasks.create') }}" class="relative mt-4 flex items-center justify-center gap-1.5 rounded-xl bg-gradient-to-r from-brand-500 to-brand-700 px-4 py-2.5 text-xs font-semibold text-white shadow-lg shadow-brand-500/20 transition hover:-translate-y-0.5">
                    Create your first task
                </a>
            @else
                <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="relative mt-4 flex items-center justify-center gap-1.5 rounded-xl border border-brand-100 bg-white px-4 py-2.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                    See what's left to do
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            @endif
        </section>
    </div>

    <div class="grid gap-5 xl:grid-cols-3">
        <section class="card p-5 sm:p-6 xl:col-span-2" aria-labelledby="upcoming-heading">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 id="upcoming-heading" class="section-title">Upcoming tasks</h2>
                    <p class="mt-0.5 text-xs text-ink-500">What's next on your calendar</p>
                </div>
                <a href="{{ route('calendar') }}" class="text-xs font-semibold text-brand-700 hover:text-brand-800">View calendar →</a>
            </div>
            <div class="mt-3 divide-y divide-brand-50">
                @forelse ($upcomingTasks as $task)
                    <x-task-row :task="$task" />
                @empty
                    <x-empty-state title="Nothing coming up yet" message="Your upcoming tasks will appear here once you schedule them." action-label="Plan a task" :action-href="route('tasks.create')" />
                @endforelse
            </div>
        </section>

        <section class="card p-5 sm:p-6" aria-labelledby="categories-heading">
            <div class="flex items-center justify-between">
                <h2 id="categories-heading" class="section-title">By category</h2>
                <span class="text-[10px] font-semibold tracking-wide text-ink-500 uppercase">{{ $categoryStats->count() }} groups</span>
            </div>
            <div class="mt-4 space-y-3.5">
                @forelse ($categoryStats as $index => $stat)
                    <div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-medium text-ink-700">{{ $stat->category }}</span>
                            <span class="text-ink-500">{{ $stat->total }} {{ Str::plural('task', $stat->total) }}</span>
                        </div>
                        <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-brand-50" role="presentation">
                            <div class="h-full rounded-full bg-gradient-to-r {{ $categoryColors[$index % count($categoryColors)] }}" style="width: {{ round(($stat->total / $maxCategory) * 100) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="rounded-xl border border-dashed border-brand-100 bg-brand-50/40 px-3 py-6 text-center text-xs text-ink-500">Add categories to your tasks to see a breakdown here.</p>
                @endforelse
            </div>

            <div class="mt-4 grid grid-cols-2 gap-2">
                <a href="{{ route('tasks.index') }}" class="rounded-xl border border-brand-100 p-2.5 text-center transition hover:border-brand-200 hover:bg-brand-50">
                    <svg class="mx-auto h-4 w-4 text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6h11M9 12h11M9 18h11"/><path d="m3 6 1.5 1.5L7 5M3 12l1.5 1.5L7 11M3 18l1.5 1.5L7 17"/></svg>
                    <span class="mt-1 block text-[10px] font-semibold text-ink-700">All tasks</span>
                </a>
                <a href="{{ route('settings') }}" class="rounded-xl border border-brand-100 p-2.5 text-center transition hover:border-brand-200 hover:bg-brand-50">
                    <svg class="mx-auto h-4 w-4 text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    <span class="mt-1 block text-[10px] font-semibold text-ink-700">Profile</span>
                </a>
            </div>
        </section>
    </div>

    <section class="card p-5 sm:p-6" aria-labelledby="recent-heading">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 id="recent-heading" class="section-title">Recent tasks</h2>
                <p class="mt-0.5 text-xs text-ink-500">The latest tasks you added</p>
            </div>
            <a href="{{ route('tasks.index', ['sort' => 'newest']) }}" class="text-xs font-semibold text-brand-700 hover:text-brand-800">See all →</a>
        </div>
        <div class="mt-3 grid gap-x-6 divide-y divide-brand-50 lg:grid-cols-2 lg:divide-y-0">
            @forelse ($recentTasks as $task)
                <x-task-row :task="$task" show-status />
            @empty
                <div class="lg:col-span-2">
                    <x-empty-state title="Your task list is a blank canvas" message="Create a task and it will show up here right away." action-label="Add your first task" :action-href="route('tasks.create')" />
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
