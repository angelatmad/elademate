import { priorityMeta, statusMeta } from '@/utils/taskMeta';

export function PriorityBadge({ value }) {
    const meta = priorityMeta(value);

    return (
        <span className={`badge ${meta.badge}`}>
            <span className="h-1.5 w-1.5 rounded-full" style={{ backgroundColor: meta.dot }} />
            {meta.label} priority
        </span>
    );
}

export function StatusBadge({ value }) {
    const meta = statusMeta(value);

    return (
        <span className={`badge ${meta.badge}`}>
            <span className="h-1.5 w-1.5 rounded-full" style={{ backgroundColor: meta.dot }} />
            {meta.label}
        </span>
    );
}

export function CategoryBadge({ category }) {
    if (!category) {
        return null;
    }

    return (
        <span className="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2.5 py-1 text-[11px] font-medium text-brand-700">
            <span className="h-1.5 w-1.5 rounded-full bg-brand-400" />
            {category}
        </span>
    );
}
