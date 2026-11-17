@props(['task', 'compact' => false])

<article x-data="{ confirmDelete: false }" class="card card-hover {{ $compact ? 'p-3.5' : 'p-4 sm:p-5' }}">
    <div class="flex items-start gap-3 sm:gap-4">
        <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="pt-0.5">
            @csrf
            @method('PATCH')
            <button type="submit" class="checkbox-circle {{ $task->isCompleted() ? 'checkbox-circle-done' : '' }}"
                    aria-label="{{ $task->isCompleted() ? 'Mark as not completed' : 'Mark as completed' }}"
                    title="{{ $task->isCompleted() ? 'Mark as not completed' : 'Mark as completed' }}">
                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 10.5 8 14l8-9"/></svg>
            </button>
        </form>

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <div class="min-w-0">
                    <a href="{{ route('tasks.show', $task) }}" class="group/title inline-flex max-w-full items-center gap-1.5">
                        <span class="truncate text-sm font-semibold transition group-hover/title:text-brand-700 sm:text-[15px] {{ $task->isCompleted() ? 'text-ink-500 line-through decoration-brand-300' : 'text-ink-900' }}">{{ $task->title }}</span>
                        <svg class="h-3.5 w-3.5 shrink-0 text-brand-300 opacity-0 transition group-hover/title:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
                    </a>
                    @unless ($compact)
                        @if ($task->description)
                            <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-ink-500 sm:text-[13px]">{{ $task->description }}</p>
                        @endif
                    @endunless
                </div>
                <div class="flex shrink-0 items-center gap-0.5">
                    <a href="{{ route('tasks.edit', $task) }}" class="icon-button" title="Edit task" aria-label="Edit {{ $task->title }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    </a>
                    <button type="button" @click="confirmDelete = true" class="icon-button hover:bg-rose-50 hover:text-rose-600" title="Delete task" aria-label="Delete {{ $task->title }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/></svg>
                    </button>
                </div>
            </div>

            <div class="mt-2.5 flex flex-wrap items-center gap-x-2 gap-y-1.5">
                <x-priority-badge :priority="$task->priority" />
                <x-status-badge :status="$task->status" />
                @if ($task->category)
                    <span class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2.5 py-1 text-[11px] font-medium text-brand-700">
                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.59 13.41 12 22l-9-9V4h9l8.59 8.59a2 2 0 0 1 0 2.82Z"/><circle cx="7.5" cy="8.5" r="1"/></svg>
                        {{ $task->category }}
                    </span>
                @endif
                <span class="inline-flex items-center gap-1 text-[11px] {{ $task->isOverdue() ? 'font-semibold text-rose-600' : 'text-ink-500' }}">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
                    {{ $task->isOverdue() ? 'Overdue · ' : '' }}{{ $task->formattedDueDate() }}
                </span>
                @if ($task->formattedDueTime())
                    <span class="inline-flex items-center gap-1 text-[11px] text-ink-500">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                        {{ $task->formattedDueTime() }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <template x-teleport="body">
        <div x-cloak x-show="confirmDelete" class="fixed inset-0 z-[70] flex items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true" aria-labelledby="delete-title-{{ $task->id }}">
            <div x-show="confirmDelete" x-transition.opacity class="absolute inset-0 bg-ink-900/45 backdrop-blur-sm" @click="confirmDelete = false"></div>
            <div x-show="confirmDelete" x-transition
                 class="relative w-full max-w-sm rounded-2xl border border-brand-100 bg-white p-5 shadow-2xl">
                <div class="flex h-11 w-11 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/></svg>
                </div>
                <h2 id="delete-title-{{ $task->id }}" class="mt-3 text-base font-semibold text-ink-900">Delete this task?</h2>
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
</article>
