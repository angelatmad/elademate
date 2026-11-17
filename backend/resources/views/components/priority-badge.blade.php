@props(['priority'])

@php
    $value = $priority instanceof \App\Enums\TaskPriority
        ? $priority
        : \App\Enums\TaskPriority::tryFrom((string) $priority) ?? \App\Enums\TaskPriority::Medium;
@endphp

<span class="badge {{ $value->badgeClasses() }}">
    <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $value->dotColor() }}"></span>
    {{ $value->label() }} priority
</span>
