@extends('layouts.app')

@section('title', 'Add Task')

@section('content')
<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-ink-500 transition hover:text-brand-700">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
                Back to my tasks
            </a>
            <h1 class="page-title mt-2">Add a new task</h1>
            <p class="mt-1 text-sm text-ink-500">Capture it now, so it doesn't live rent-free in your head.</p>
        </div>
    </div>

    @include('tasks.partials.form', [
        'task' => $task,
        'categories' => $categories,
        'priorities' => $priorities,
        'statuses' => $statuses,
        'action' => route('tasks.store'),
        'method' => 'POST',
        'submitLabel' => 'Save Task',
        'cancelUrl' => route('tasks.index'),
    ])
</div>
@endsection
