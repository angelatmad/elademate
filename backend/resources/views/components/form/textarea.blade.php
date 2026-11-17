@props([
    'label',
    'name',
    'value' => '',
    'placeholder' => '',
    'rows' => 4,
    'required' => false,
    'hint' => null,
])

<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="label">
        {{ $label }}
        @if ($required)
            <span class="text-brand-500" aria-hidden="true">*</span>
        @endif
    </label>
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if ($required) required aria-required="true" @endif
        @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
        {{ $attributes->except('class')->merge(['class' => 'input resize-y']) }}
    >{{ old($name, $value) }}</textarea>
    @if ($hint)
        <p class="mt-1.5 text-xs text-ink-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $name }}-error" class="error-text">
            <svg class="mt-0.5 h-3.5 w-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-8-4a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0V7a1 1 0 0 1 1-1Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
            {{ $message }}
        </p>
    @enderror
</div>
