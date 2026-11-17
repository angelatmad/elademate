@php
    $user = auth()->user();
    $links = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
        ['route' => 'tasks.index', 'label' => 'My Tasks', 'icon' => 'list'],
        ['route' => 'calendar', 'label' => 'Calendar', 'icon' => 'calendar'],
        ['route' => 'settings', 'label' => 'Settings', 'icon' => 'settings'],
    ];
@endphp

<div class="lg:hidden">
    <div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-ink-900/40 backdrop-blur-sm" @click="sidebarOpen = false" aria-hidden="true"></div>
</div>

<aside
    x-cloak
    class="fixed inset-y-0 left-0 z-50 flex w-[272px] -translate-x-full flex-col border-r border-brand-100/80 bg-white transition-transform duration-300 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    aria-label="Main sidebar"
>
    <div class="flex h-[76px] items-center justify-between border-b border-brand-100/70 px-6">
        <x-logo />
        <button type="button" class="icon-button lg:hidden" @click="sidebarOpen = false" aria-label="Close navigation">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-6">
        <p class="px-3 text-[10px] font-semibold tracking-[0.2em] text-ink-500/70 uppercase">Menu</p>
        <nav class="mt-3 space-y-1.5" aria-label="Main navigation">
            @foreach ($links as $link)
                @php
                    $active = request()->routeIs($link['route']) || ($link['route'] === 'tasks.index' && request()->routeIs('tasks.*'));
                @endphp
                <a href="{{ route($link['route']) }}" @click="sidebarOpen = false"
                   class="nav-link {{ $active ? 'nav-link-active' : '' }}"
                   @if ($active) aria-current="page" @endif>
                    @if ($link['icon'] === 'home')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 10.5 9-7 9 7"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/></svg>
                    @elseif ($link['icon'] === 'list')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6h11M9 12h11M9 18h11"/><path d="m3 6 1.5 1.5L7 5M3 12l1.5 1.5L7 11M3 18l1.5 1.5L7 17"/></svg>
                    @elseif ($link['icon'] === 'calendar')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 17.5h.01M12 17.5h.01"/></svg>
                    @else
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1.08-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1.08 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c.26.604.852.997 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>
                    @endif
                    <span>{{ $link['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="mt-6 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 p-4 text-white shadow-lg shadow-brand-500/25">
            <div class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                </span>
                <p class="text-sm font-semibold">Stay on track</p>
            </div>
            <p class="mt-2 text-xs leading-relaxed text-white/80">A little progress each day adds up to big results.</p>
            <a href="{{ route('tasks.create') }}" @click="sidebarOpen = false" class="mt-3 flex items-center justify-center gap-1.5 rounded-lg bg-white px-3 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                Add new task
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        </div>
    </div>

    <div class="border-t border-brand-100/70 p-4">
        <div class="flex items-center gap-3 rounded-2xl bg-brand-50/70 p-3">
            <x-avatar :user="$user" size="sm" />
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-ink-900">{{ $user->name }}</p>
                <p class="truncate text-[11px] text-ink-500">{{ $user->email }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="icon-button" title="Log out" aria-label="Log out">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>
