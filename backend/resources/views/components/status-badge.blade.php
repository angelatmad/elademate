@props(['status'])

@php
    $value = $status instanceof \App\Enums\TaskStatus
        ? $status
        : \App\Enums\TaskStatus::tryFrom((string) $status) ?? \App\Enums\TaskStatus::Pending;
@endphp

<span class="badge {{ $value->badgeClasses() }}">
    <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $value->dotColor() }}"></span>
    {{ $value->label() }}
</span>
