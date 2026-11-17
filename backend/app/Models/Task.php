<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'HCI 1',
        'OOP 2',
        'IM 2',
        'APPsDEV',
        'OS',
        'NET 2',
        'FBA',
        'IAS',
        'SOC SCI 1',
        // Legacy categories kept so existing tasks remain valid and editable.
        'NET',
        'Personal',
        'Organization',
        'School',
        'Health',
        'Other',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'due_date',
        'due_time',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'priority' => TaskPriority::class,
            'status' => TaskStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === TaskStatus::Completed;
    }

    public function isOverdue(): bool
    {
        if ($this->isCompleted() || ! $this->due_date) {
            return false;
        }

        if ($this->due_date->isBefore(today())) {
            return true;
        }

        return $this->due_date->isToday()
            && filled($this->due_time)
            && $this->due_time < now()->format('H:i:s');
    }

    public function formattedDueDate(): string
    {
        return $this->due_date?->format('M j, Y') ?? 'No due date';
    }

    public function formattedDueTime(): string
    {
        if (! filled($this->due_time)) {
            return '';
        }

        return date('g:i A', strtotime($this->due_time));
    }

    public function timeForInput(): string
    {
        return filled($this->due_time) ? substr($this->due_time, 0, 5) : '';
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! filled($term)) {
            return $query;
        }

        return $query->where(function ($query) use ($term) {
            $query->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('category', 'like', "%{$term}%");
        });
    }

    public function scopeStatus($query, ?string $status)
    {
        if (! filled($status) || ! in_array($status, TaskStatus::values(), true)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopePriority($query, ?string $priority)
    {
        if (! filled($priority) || ! in_array($priority, TaskPriority::values(), true)) {
            return $query;
        }

        return $query->where('priority', $priority);
    }

    public function scopeCategory($query, ?string $category)
    {
        if (! filled($category) || ! in_array($category, self::CATEGORIES, true)) {
            return $query;
        }

        return $query->where('category', $category);
    }
}
