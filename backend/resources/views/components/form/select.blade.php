@props([
    'label',
    'name',
    'options' => [],
    'selected' => '',
    'placeholder' => null,
    'required' => false,
])

<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="label">
        {{ $label }}
        @if ($required)
            <span class="text-brand-500" aria-hidden="true">*</span>
        @endif
    </label>
    <div class="relative">
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            @if ($required) required aria-required="true" @endif
            @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
            {{ $attributes->except('class')->merge(['class' => 'input appearance-none pr-10']) }}
        >
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected((string) old($name, $selected) === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach
        </select>
        <svg class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
        </svg>
    </div>
    @error($name)
        <p id="{{ $name }}-error" class="error-text">
            <svg class="mt-0.5 h-3.5 w-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-8-4a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0V7a1 1 0 0 1 1-1Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
            {{ $message }}
        </p>
    @enderror
</div>
