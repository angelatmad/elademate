@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="mx-auto max-w-5xl space-y-5" x-data="{ confirmDelete: false }">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('tasks.show', $task) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-ink-500 transition hover:text-brand-700">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
                Back to task details
            </a>
            <h1 class="page-title mt-2">Edit task</h1>
            <p class="mt-1 text-sm text-ink-500">Update the details and keep your plan current.</p>
        </div>
        <button type="button" @click="confirmDelete = true" class="btn-ghost !text-rose-600 hover:!bg-rose-50">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/></svg>
            Delete task
        </button>
    </div>

    @include('tasks.partials.form', [
        'task' => $task,
        'categories' => $categories,
        'priorities' => $priorities,
        'statuses' => $statuses,
        'action' => route('tasks.update', $task),
        'method' => 'PUT',
        'submitLabel' => 'Save Changes',
        'cancelUrl' => route('tasks.show', $task),
    ])

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
