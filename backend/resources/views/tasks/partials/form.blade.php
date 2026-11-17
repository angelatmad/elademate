@php
    $selectedPriority = old('priority', $task->priority?->value ?? 'medium');
    $selectedStatus = old('status', $task->status?->value ?? 'pending');
    $dueDate = old('due_date', $task->due_date?->format('Y-m-d'));
    $dueTime = old('due_time', $task->timeForInput());
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5" novalidate>
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-5 xl:grid-cols-3">
        <div class="space-y-5 xl:col-span-2">
            <section class="card p-5 sm:p-6" aria-labelledby="details-heading">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    </span>
                    <div>
                        <h2 id="details-heading" class="section-title">Task details</h2>
                        <p class="text-xs text-ink-500">Keep it clear and easy to come back to.</p>
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    <x-form.input label="Task title" name="title" :value="$task->title" placeholder="e.g. Finish HCI final project paper" required autofocus />
                    <x-form.textarea label="Description" name="description" :value="$task->description" placeholder="Add notes, links, or reminders for future you..." rows="5" hint="Optional — but helpful when the task has a few steps." />

                    <div>
                        <label for="category" class="label">Category</label>
                        <div class="relative">
                            <select id="category" name="category" class="input appearance-none pr-10">
                                <option value="">Choose a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category', $task->category) === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                            <svg class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
                        </div>
                        @error('category')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section class="card p-5 sm:p-6" aria-labelledby="schedule-heading">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
                    </span>
                    <div>
                        <h2 id="schedule-heading" class="section-title">Schedule</h2>
                        <p class="text-xs text-ink-500">Give this task a place on your calendar.</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="due_date" class="label">Due date</label>
                        <input id="due_date" name="due_date" type="date" value="{{ $dueDate }}" class="input" @error('due_date') aria-invalid="true" aria-describedby="due_date-error" @enderror>
                        @error('due_date')
                            <p id="due_date-error" class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="due_time" class="label">Due time</label>
                        <input id="due_time" name="due_time" type="time" value="{{ $dueTime }}" class="input" @error('due_time') aria-invalid="true" aria-describedby="due_time-error" @enderror>
                        @error('due_time')
                            <p id="due_time-error" class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>
        </div>

        <div class="space-y-5">
            <section class="card p-5 sm:p-6" aria-labelledby="priority-heading">
                <h2 id="priority-heading" class="section-title">Priority</h2>
                <p class="mt-0.5 text-xs text-ink-500">How much attention does this need?</p>

                <div class="mt-4 space-y-2">
                    @foreach ($priorities as $priority)
                        <label class="block cursor-pointer">
                            <input type="radio" name="priority" value="{{ $priority->value }}" class="peer sr-only" @checked($selectedPriority === $priority->value)>
                            <span class="flex items-center justify-between rounded-xl border border-brand-100 bg-white px-3.5 py-2.5 text-sm text-ink-700 transition peer-checked:border-brand-400 peer-checked:bg-brand-50 peer-checked:text-brand-800 peer-focus-visible:ring-4 peer-focus-visible:ring-brand-100 hover:border-brand-200">
                                <span class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full" style="background-color: {{ $priority->dotColor() }}"></span>
                                    <span class="font-medium">{{ $priority->label() }}</span>
                                </span>
                                <svg class="h-4 w-4 text-brand-500 opacity-0 transition peer-checked:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('priority')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </section>

            <section class="card p-5 sm:p-6" aria-labelledby="status-heading">
                <h2 id="status-heading" class="section-title">Status</h2>
                <p class="mt-0.5 text-xs text-ink-500">Where is this task right now?</p>

                <div class="mt-4">
                    <label for="status" class="sr-only">Task status</label>
                    <select id="status" name="status" class="input appearance-none">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <section class="rounded-2xl border border-brand-100 bg-gradient-to-br from-brand-50 to-white p-5">
                <div class="flex items-center gap-2 text-brand-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/><circle cx="12" cy="12" r="3"/></svg>
                    <p class="text-xs font-semibold">A small tip</p>
                </div>
                <p class="mt-2 text-xs leading-relaxed text-ink-500">Break big tasks into clear titles. "Draft HCI chapter 3" is easier to start than "Do project".</p>
            </section>
        </div>
    </div>

    <div class="flex flex-col-reverse gap-2 rounded-2xl border border-brand-100 bg-white p-3.5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-5">
        <a href="{{ $cancelUrl ?? route('tasks.index') }}" class="btn-ghost justify-center">Cancel</a>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <p class="hidden text-[11px] text-ink-500 sm:block">Saved tasks update your dashboard and calendar instantly.</p>
            <button type="submit" class="btn-primary">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
                {{ $submitLabel ?? 'Save Task' }}
            </button>
        </div>
    </div>
</form>
