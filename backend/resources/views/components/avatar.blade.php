@props(['user', 'size' => 'md'])

@php
    $sizes = [
        'xs' => 'h-8 w-8 text-[10px]',
        'sm' => 'h-9 w-9 text-xs',
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-20 w-20 text-2xl',
        'xl' => 'h-24 w-24 text-3xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

@if ($user->avatar_url)
    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }} profile photo"
         {{ $attributes->merge(['class' => "rounded-full object-cover ring-2 ring-white shadow-sm {$sizeClass}"]) }}>
@else
    <span {{ $attributes->merge(['class' => "inline-flex shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-brand-400 to-brand-700 font-semibold text-white ring-2 ring-white shadow-sm {$sizeClass}", 'aria-label' => $user->name]) }}>
        {{ $user->initials }}
    </span>
@endif
