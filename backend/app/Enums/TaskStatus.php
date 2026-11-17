<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InProgress => 'In Progress',
            self::Completed => 'Completed',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-slate-100 text-slate-600 ring-slate-200',
            self::InProgress => 'bg-sky-50 text-sky-700 ring-sky-200',
            self::Completed => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            self::Pending => '#94a3b8',
            self::InProgress => '#0ea5e9',
            self::Completed => '#10b981',
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
            ->mapWithKeys(fn (self $status) => [$status->value => $status->label()])
            ->all();
    }
}
