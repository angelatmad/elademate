@props([
    'title' => 'Nothing here yet',
    'message' => 'Add your first task to get started.',
    'actionLabel' => null,
    'actionHref' => null,
])

<div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-brand-200 bg-brand-50/40 px-6 py-12 text-center">
    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-brand-500 shadow-sm">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M9 6h11M9 12h11M9 18h11"/><path d="m3 6 1.5 1.5L7 5M3 12l1.5 1.5L7 11M3 18l1.5 1.5L7 17"/>
        </svg>
    </span>
    <h3 class="mt-4 text-sm font-semibold text-ink-900">{{ $title }}</h3>
    <p class="mt-1 max-w-xs text-xs leading-relaxed text-ink-500">{{ $message }}</p>
    @if ($actionLabel && $actionHref)
        <a href="{{ $actionHref }}" class="btn-primary mt-4">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            {{ $actionLabel }}
        </a>
    @endif
    {{ $slot }}
</div>
