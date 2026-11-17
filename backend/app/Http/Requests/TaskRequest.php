<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $creating = $this->isMethod('post');
        $scheduleRule = $creating ? 'required' : 'nullable';
        $categoryRule = $creating ? 'required' : 'nullable';

        return [
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => [$categoryRule, 'string', Rule::in(Task::CATEGORIES)],
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'status' => ['required', Rule::enum(TaskStatus::class)],
            'due_date' => [$scheduleRule, 'date', 'after_or_equal:2000-01-01'],
            'due_time' => [$scheduleRule, 'date_format:H:i'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (filled($this->input('due_time')) && blank($this->input('due_date'))) {
                $validator->errors()->add('due_date', 'Please choose a due date for the selected due time.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Give your task a title so you can find it later.',
            'category.required' => 'Select a subject for this task.',
            'priority.required' => 'Select a priority for this task.',
            'status.required' => 'Select a status for this task.',
            'due_date.required' => 'Choose a due date for this task.',
            'due_time.required' => 'Choose a due time for this task.',
            'due_time.date_format' => 'Enter the due time in 24-hour format (e.g. 14:30).',
        ];
    }
}
