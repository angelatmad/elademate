@php
    $user = auth()->user();
@endphp

<header class="sticky top-0 z-30 border-b border-brand-100/70 bg-white/85 backdrop-blur-md">
    <div class="flex h-[76px] items-center gap-3 px-4 sm:px-6 lg:px-8">
        <button type="button" class="icon-button lg:hidden" @click="sidebarOpen = true" aria-label="Open navigation" aria-controls="main-sidebar">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>

        <a href="{{ route('dashboard') }}" class="lg:hidden" aria-label="EladeMate dashboard">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-md shadow-brand-500/25">
                <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
            </span>
        </a>

        <form method="GET" action="{{ route('tasks.index') }}" class="relative min-w-0 flex-1 sm:max-w-md" role="search">
            <label for="global-search" class="sr-only">Search tasks</label>
            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-ink-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input id="global-search" name="q" type="search" value="{{ request()->routeIs('tasks.index') ? request('q') : '' }}"
                   placeholder="Search tasks, notes, categories..."
                   class="w-full rounded-xl border border-brand-100 bg-brand-50/60 py-2.5 pr-4 pl-10 text-sm text-ink-900 transition placeholder:text-ink-500/70 focus:border-brand-300 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-100">
        </form>

        <div class="ml-auto flex items-center gap-1.5 sm:gap-2">
            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                <button type="button" @click="open = !open" :aria-expanded="open" class="icon-button relative" aria-label="Notifications" aria-haspopup="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
                    @if ($topbarTaskCount > 0)
                        <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-brand-500 px-1 text-[9px] font-bold text-white ring-2 ring-white">{{ $topbarTaskCount > 9 ? '9+' : $topbarTaskCount }}</span>
                    @endif
                </button>

                <div x-cloak x-show="open" x-transition.origin.top.right
                     class="absolute right-0 mt-2 w-80 overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-[0_24px_60px_-20px_rgba(194,24,91,0.35)]">
                    <div class="flex items-center justify-between border-b border-brand-50 px-4 py-3">
                        <p class="text-sm font-semibold text-ink-900">Due soon</p>
                        <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-semibold text-brand-700">{{ $topbarTaskCount }}</span>
                    </div>
                    <div class="max-h-72 overflow-y-auto">
                        @forelse ($topbarTasks as $task)
                            <a href="{{ route('tasks.show', $task) }}" class="flex items-start gap-3 border-b border-brand-50/70 px-4 py-3 transition last:border-0 hover:bg-brand-50/50">
                                <span class="mt-1 h-2 w-2 shrink-0 rounded-full" style="background-color: {{ $task->priority->dotColor() }}"></span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-medium text-ink-900">{{ $task->title }}</span>
                                    <span class="mt-0.5 block text-[11px] {{ $task->isOverdue() ? 'text-rose-600' : 'text-ink-500' }}">
                                        {{ $task->isOverdue() ? 'Overdue' : 'Due' }} {{ $task->formattedDueDate() }}{{ $task->formattedDueTime() ? ' at '.$task->formattedDueTime() : '' }}
                                    </span>
                                </span>
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center">
                                <span class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                                </span>
                                <p class="mt-2 text-sm font-semibold text-ink-900">You're all caught up!</p>
                                <p class="mt-0.5 text-xs text-ink-500">No tasks due in the next 3 days.</p>
                            </div>
                        @endforelse
                    </div>
                    <a href="{{ route('tasks.index') }}" class="block bg-brand-50/50 px-4 py-2.5 text-center text-xs font-semibold text-brand-700 transition hover:bg-brand-50">View all tasks</a>
                </div>
            </div>

            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                <button type="button" @click="open = !open" :aria-expanded="open" class="flex items-center gap-2 rounded-xl p-1 pr-1.5 transition hover:bg-brand-50 focus:outline-none focus-visible:ring-4 focus-visible:ring-brand-100 sm:pr-2" aria-label="Open profile menu" aria-haspopup="true">
                    <x-avatar :user="$user" size="sm" />
                    <span class="hidden text-left sm:block">
                        <span class="block max-w-[120px] truncate text-xs font-semibold text-ink-900">{{ $user->name }}</span>
                        <span class="block text-[10px] text-ink-500">Student</span>
                    </span>
                    <svg class="hidden h-3.5 w-3.5 text-ink-500 sm:block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                </button>

                <div x-cloak x-show="open" x-transition.origin.top.right
                     class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-[0_24px_60px_-20px_rgba(194,24,91,0.35)]">
                    <div class="border-b border-brand-50 px-4 py-3">
                        <p class="truncate text-sm font-semibold text-ink-900">{{ $user->name }}</p>
                        <p class="truncate text-[11px] text-ink-500">{{ $user->email }}</p>
                    </div>
                    <div class="p-1.5">
                        <a href="{{ route('settings') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-ink-700 transition hover:bg-brand-50 hover:text-brand-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                            Profile settings
                        </a>
                        <a href="{{ route('tasks.index') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-ink-700 transition hover:bg-brand-50 hover:text-brand-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6h11M9 12h11M9 18h11"/><path d="m3 6 1.5 1.5L7 5M3 12l1.5 1.5L7 11M3 18l1.5 1.5L7 17"/></svg>
                            My tasks
                        </a>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-brand-50 p-1.5">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/></svg>
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
