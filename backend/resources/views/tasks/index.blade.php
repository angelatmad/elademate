@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')
@php
    $statusChips = [
        ['key' => '', 'label' => 'All tasks', 'count' => $counts['all']],
        ['key' => 'pending', 'label' => 'Pending', 'count' => $counts['pending']],
        ['key' => 'in_progress', 'label' => 'In Progress', 'count' => $counts['in_progress']],
        ['key' => 'completed', 'label' => 'Completed', 'count' => $counts['completed']],
    ];
    $query = request()->query();
@endphp

<div class="space-y-5">
    <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-medium text-ink-500">Your workspace</p>
            <h1 class="page-title mt-1">My Tasks</h1>
            <p class="mt-1 text-sm text-ink-500">Everything you need to do, in one organized list.</p>
        </div>
        <a href="{{ route('tasks.create') }}" class="btn-primary shrink-0">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Add Task
        </a>
    </section>

    <section class="flex flex-wrap gap-2" aria-label="Filter tasks by status">
        @foreach ($statusChips as $chip)
            @php
                $chipQuery = array_filter(array_merge($query, ['status' => $chip['key']]), fn ($value) => $value !== '' && $value !== null);
            @endphp
            <a href="{{ route('tasks.index', $chipQuery) }}"
               class="chip {{ ($filters['status'] ?? '') === $chip['key'] ? 'chip-active' : '' }}"
               @if (($filters['status'] ?? '') === $chip['key']) aria-current="true" @endif>
                {{ $chip['label'] }}
                <span class="rounded-full {{ ($filters['status'] ?? '') === $chip['key'] ? 'bg-white/25' : 'bg-brand-50' }} px-1.5 py-0.5 text-[10px] font-bold">{{ $chip['count'] }}</span>
            </a>
        @endforeach
    </section>

    <section class="card p-4 sm:p-5" aria-label="Search and filters">
        <form method="GET" action="{{ route('tasks.index') }}" class="grid gap-3 md:grid-cols-12 md:items-end">
            <div class="md:col-span-4">
                <label for="q" class="label">Search</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input id="q" name="q" type="search" value="{{ $filters['q'] }}" placeholder="Search title, notes, category..."
                           class="input pl-9">
                </div>
            </div>
            <div class="md:col-span-2">
                <label for="priority" class="label">Priority</label>
                <select id="priority" name="priority" class="input appearance-none">
                    <option value="">All priorities</option>
                    @foreach ($priorities as $value => $label)
                        <option value="{{ $value }}" @selected($filters['priority'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="category" class="label">Category</label>
                <select id="category" name="category" class="input appearance-none">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="sort" class="label">Sort by</label>
                <select id="sort" name="sort" class="input appearance-none">
                    <option value="due_date" @selected($filters['sort'] === 'due_date')>Due date (soonest)</option>
                    <option value="due_date_desc" @selected($filters['sort'] === 'due_date_desc')>Due date (latest)</option>
                    <option value="newest" @selected($filters['sort'] === 'newest')>Newest first</option>
                    <option value="oldest" @selected($filters['sort'] === 'oldest')>Oldest first</option>
                    <option value="title" @selected($filters['sort'] === 'title')>Title A–Z</option>
                </select>
            </div>
            <div class="flex items-center gap-2 md:col-span-2">
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
                <button type="submit" class="btn-primary flex-1 !px-4">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                    Filter
                </button>
                <a href="{{ route('tasks.index') }}" class="btn-ghost !px-3" title="Reset filters" aria-label="Reset all filters">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
                </a>
            </div>
        </form>

        @if ($filters['q'] || $filters['status'] || $filters['priority'] || $filters['category'])
            <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-brand-50 pt-3 text-[11px] text-ink-500">
                <span class="font-semibold text-ink-700">Active filters:</span>
                @if ($filters['q'])<span class="chip !py-1">“{{ $filters['q'] }}”</span>@endif
                @if ($filters['status'])<span class="chip !py-1">{{ $statuses[$filters['status']] ?? $filters['status'] }}</span>@endif
                @if ($filters['priority'])<span class="chip !py-1">{{ $priorities[$filters['priority']] ?? $filters['priority'] }} priority</span>@endif
                @if ($filters['category'])<span class="chip !py-1">{{ $filters['category'] }}</span>@endif
            </div>
        @endif
    </section>

    <section aria-label="Task list">
        <div class="mb-3 flex items-center justify-between gap-3">
            <p class="text-xs font-medium text-ink-500">
                <span class="font-semibold text-ink-900">{{ $tasks->total() }}</span>
                {{ Str::plural('task', $tasks->total()) }} found
            </p>
            @if ($tasks->count() > 0)
                <p class="text-[11px] text-ink-500">Page {{ $tasks->currentPage() }} of {{ $tasks->lastPage() }}</p>
            @endif
        </div>

        <div class="space-y-3">
            @forelse ($tasks as $task)
                <x-task-card :task="$task" />
            @empty
                <x-empty-state
                    title="{{ $filters['q'] || $filters['status'] || $filters['priority'] || $filters['category'] ? 'No tasks match your filters' : 'Your task list is ready for its first task' }}"
                    message="{{ $filters['q'] || $filters['status'] || $filters['priority'] || $filters['category'] ? 'Try a different search, clear a filter, or start a new task.' : 'Add an assignment, org duty, or personal goal — EladeMate will help you keep track.' }}"
                    action-label="Add a task"
                    :action-href="route('tasks.create')"
                >
                    @if ($filters['q'] || $filters['status'] || $filters['priority'] || $filters['category'])
                        <a href="{{ route('tasks.index') }}" class="btn-ghost mt-2 !py-2 text-xs">Clear all filters</a>
                    @endif
                </x-empty-state>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $tasks->links('vendor.pagination.elademate') }}
        </div>
    </section>
</div>
@endsection
