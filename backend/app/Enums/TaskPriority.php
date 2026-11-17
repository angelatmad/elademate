<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Low => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::Medium => 'bg-amber-50 text-amber-700 ring-amber-200',
            self::High => 'bg-rose-50 text-rose-700 ring-rose-200',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            self::Low => '#10b981',
            self::Medium => '#f59e0b',
            self::High => '#e91e63',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $priority) => [$priority->value => $priority->label()])
            ->all();
    }
}
