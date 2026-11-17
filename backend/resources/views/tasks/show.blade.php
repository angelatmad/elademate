@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="mx-auto max-w-5xl space-y-5" x-data="{ confirmDelete: false }">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-ink-500 transition hover:text-brand-700">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
            Back to my tasks
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('tasks.edit', $task) }}" class="btn-secondary">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                Edit
            </a>
            <button type="button" @click="confirmDelete = true" class="btn-ghost !text-rose-600 hover:!bg-rose-50">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/></svg>
                Delete
            </button>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <section class="card p-5 sm:p-7 lg:col-span-2" aria-labelledby="task-title">
            <div class="flex items-start gap-4">
                <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="pt-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="checkbox-circle h-8 w-8 {{ $task->isCompleted() ? 'checkbox-circle-done' : '' }}"
                            aria-label="{{ $task->isCompleted() ? 'Mark as not completed' : 'Mark as completed' }}">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 10.5 8 14l8-9"/></svg>
                    </button>
                </form>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap gap-2">
                        <x-priority-badge :priority="$task->priority" />
                        <x-status-badge :status="$task->status" />
                        @if ($task->isOverdue())
                            <span class="badge bg-rose-50 text-rose-700 ring-rose-200">
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 8v5M12 16h.01"/><circle cx="12" cy="12" r="9"/></svg>
                                Overdue
                            </span>
                        @endif
                    </div>

                    <h1 id="task-title" class="mt-3 text-xl font-bold tracking-tight text-ink-900 sm:text-2xl {{ $task->isCompleted() ? 'text-ink-500 line-through decoration-brand-300' : '' }}">{{ $task->title }}</h1>

                    @if ($task->category)
                        <p class="mt-1.5 inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-1 text-[11px] font-medium text-brand-700">
                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.59 13.41 12 22l-9-9V4h9l8.59 8.59a2 2 0 0 1 0 2.82Z"/><circle cx="7.5" cy="8.5" r="1"/></svg>
                            {{ $task->category }}
                        </p>
                    @endif

                    <div class="mt-5 rounded-2xl bg-brand-50/50 p-4">
                        <h2 class="text-[11px] font-semibold tracking-[0.14em] text-ink-500 uppercase">Description</h2>
                        <p class="mt-2 text-sm leading-relaxed whitespace-pre-line {{ $task->description ? 'text-ink-700' : 'text-ink-500 italic' }}">
                            {{ $task->description ?: 'No description added yet. Edit this task to add notes, links, or reminders.' }}
                        </p>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-brand-100 p-3.5">
                            <p class="flex items-center gap-1.5 text-[10px] font-semibold tracking-wide text-ink-500 uppercase">
                                <svg class="h-3.5 w-3.5 text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
                                Due date
                            </p>
                            <p class="mt-1 text-sm font-semibold {{ $task->isOverdue() ? 'text-rose-600' : 'text-ink-900' }}">{{ $task->formattedDueDate() }}</p>
                        </div>
                        <div class="rounded-xl border border-brand-100 p-3.5">
                            <p class="flex items-center gap-1.5 text-[10px] font-semibold tracking-wide text-ink-500 uppercase">
                                <svg class="h-3.5 w-3.5 text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                Due time
                            </p>
                            <p class="mt-1 text-sm font-semibold text-ink-900">{{ $task->formattedDueTime() ?: 'No specific time' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <aside class="space-y-5">
            <section class="card p-5" aria-labelledby="task-info-heading">
                <h2 id="task-info-heading" class="section-title">Task information</h2>
                <dl class="mt-4 space-y-3.5 text-xs">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-ink-500">Status</dt>
                        <dd><x-status-badge :status="$task->status" /></dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-ink-500">Priority</dt>
                        <dd><x-priority-badge :priority="$task->priority" /></dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-ink-500">Category</dt>
                        <dd class="font-medium text-ink-900">{{ $task->category ?: 'Uncategorized' }}</dd>
                    </div>
                    <div class="border-t border-brand-50 pt-3">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-ink-500">Created</dt>
                            <dd class="font-medium text-ink-900">{{ $task->created_at->format('M j, Y') }}</dd>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-ink-500">Last updated</dt>
                        <dd class="font-medium text-ink-900">{{ $task->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 p-5 text-white shadow-lg shadow-brand-500/25">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                    </span>
                    <p class="text-sm font-semibold">{{ $task->isCompleted() ? 'One less thing to carry' : 'You can do this' }}</p>
                </div>
                <p class="mt-2 text-xs leading-relaxed text-white/80">
                    {{ $task->isCompleted() ? 'This task is complete. Take a breath and celebrate the small win.' : 'Start with the smallest next step. Progress beats perfection every time.' }}
                </p>
                <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="mt-3">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-white px-3 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                        {{ $task->isCompleted() ? 'Mark as active again' : 'Mark as completed' }}
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                </form>
            </section>

            <a href="{{ route('calendar') }}" class="flex items-center justify-between gap-3 rounded-2xl border border-brand-100 bg-white p-4 text-xs font-semibold text-ink-700 transition hover:border-brand-200 hover:bg-brand-50">
                See this task on the calendar
                <svg class="h-4 w-4 text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </aside>
    </div>

    <template x-teleport="body">
        <div x-cloak x-show="confirmDelete" class="fixed inset-0 z-[70] flex items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true" aria-labelledby="delete-task-title">
            <div x-show="confirmDelete" x-transition.opacity class="absolute inset-0 bg-ink-900/45 backdrop-blur-sm" @click="confirmDelete = false"></div>
            <div x-show="confirmDelete" x-transition class="relative w-full max-w-sm rounded-2xl border border-brand-100 bg-white p-5 shadow-2xl">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/></svg>
                </div>
                <h2 id="delete-task-title" class="mt-3 text-base font-semibold text-ink-900">Delete this task?</h2>
                <p class="mt-1 text-sm leading-relaxed text-ink-500">"{{ $task->title }}" will be permanently removed. This action cannot be undone.</p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="confirmDelete = false" class="btn-secondary">Keep task</button>
                    <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
