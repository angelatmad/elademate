@props(['task', 'showStatus' => false])

<div class="group flex items-start gap-3 rounded-xl px-2 py-2.5 transition hover:bg-brand-50/60 sm:px-3">
    <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="pt-0.5">
        @csrf
        @method('PATCH')
        <button type="submit" class="checkbox-circle h-5 w-5 {{ $task->isCompleted() ? 'checkbox-circle-done' : '' }}"
                aria-label="{{ $task->isCompleted() ? 'Mark as not completed' : 'Mark as completed' }}">
            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 10.5 8 14l8-9"/></svg>
        </button>
    </form>

    <a href="{{ route('tasks.show', $task) }}" class="min-w-0 flex-1">
        <span class="block truncate text-[13px] font-medium transition group-hover:text-brand-700 {{ $task->isCompleted() ? 'text-ink-500 line-through decoration-brand-300' : 'text-ink-900' }}">{{ $task->title }}</span>
        <span class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px] text-ink-500">
            @if ($task->category)
                <span>{{ $task->category }}</span>
                <span class="h-1 w-1 rounded-full bg-brand-200" aria-hidden="true"></span>
            @endif
            <span class="{{ $task->isOverdue() ? 'font-semibold text-rose-600' : '' }}">
                {{ $task->isOverdue() ? 'Overdue · ' : '' }}{{ $task->due_date ? $task->due_date->format('M j') : 'No due date' }}{{ $task->formattedDueTime() ? ' · '.$task->formattedDueTime() : '' }}
            </span>
        </span>
    </a>

    <div class="flex shrink-0 items-center gap-1.5">
        @if ($showStatus)
            <x-status-badge :status="$task->status" />
        @else
            <x-priority-badge :priority="$task->priority" />
        @endif
    </div>
</div>
