@props(['light' => false, 'href' => null])

@php
    $link = $href ?? (auth()->check() ? route('dashboard') : route('home'));
@endphp

<a href="{{ $link }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }} aria-label="EladeMate home">
    <span class="relative flex h-10 w-10 items-center justify-center rounded-[14px] bg-gradient-to-br from-brand-400 via-brand-500 to-brand-700 shadow-lg shadow-brand-500/30">
        <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 6 9 17l-5-5" />
        </svg>
        <span class="absolute -right-1 -top-1 h-3 w-3 rounded-full border-2 border-white bg-brand-200"></span>
    </span>
    <span class="leading-none">
        <span class="block text-[21px] font-bold tracking-tight {{ $light ? 'text-white' : 'text-ink-900' }}">
            Elade<span class="{{ $light ? 'text-brand-100' : 'text-brand-600' }}">Mate</span>
        </span>
        <span class="mt-1 block text-[9px] font-semibold tracking-[0.22em] uppercase {{ $light ? 'text-white/70' : 'text-ink-500' }}">Study. Plan. Thrive.</span>
    </span>
</a>
