@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Task pagination" class="flex flex-col items-center justify-between gap-3 border-t border-brand-50 px-1 pt-4 sm:flex-row">
        <p class="text-xs text-ink-500">
            Showing <span class="font-semibold text-ink-700">{{ $paginator->firstItem() }}</span>–<span class="font-semibold text-ink-700">{{ $paginator->lastItem() }}</span>
            of <span class="font-semibold text-ink-700">{{ $paginator->total() }}</span> tasks
        </p>

        <div class="flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-brand-50 text-brand-200" aria-hidden="true">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex h-8 w-8 items-center justify-center rounded-lg border border-brand-100 text-ink-500 transition hover:bg-brand-50 hover:text-brand-700" aria-label="Previous page">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="flex h-8 w-8 items-center justify-center text-xs text-ink-500">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="flex h-8 min-w-8 items-center justify-center rounded-lg bg-brand-500 px-2 text-xs font-semibold text-white shadow-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="flex h-8 min-w-8 items-center justify-center rounded-lg border border-brand-100 px-2 text-xs font-medium text-ink-500 transition hover:bg-brand-50 hover:text-brand-700">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex h-8 w-8 items-center justify-center rounded-lg border border-brand-100 text-ink-500 transition hover:bg-brand-50 hover:text-brand-700" aria-label="Next page">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            @else
                <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-brand-50 text-brand-200" aria-hidden="true">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
