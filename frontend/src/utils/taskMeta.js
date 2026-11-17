export const CATEGORIES = ['HCI 1', 'OOP 2', 'IM 2', 'APPsDEV', 'OS', 'NET 2', 'FBA', 'IAS', 'SOC SCI 1'];

export const PRIORITIES = [
    {
        value: 'low',
        label: 'Low',
        dot: '#10b981',
        badge: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    },
    {
        value: 'medium',
        label: 'Medium',
        dot: '#f59e0b',
        badge: 'bg-amber-50 text-amber-700 ring-amber-200',
    },
    {
        value: 'high',
        label: 'High',
        dot: '#e91e63',
        badge: 'bg-rose-50 text-rose-700 ring-rose-200',
    },
];

export const STATUSES = [
    {
        value: 'pending',
        label: 'Pending',
        dot: '#94a3b8',
        badge: 'bg-slate-100 text-slate-600 ring-slate-200',
    },
    {
        value: 'in_progress',
        label: 'In Progress',
        dot: '#0ea5e9',
        badge: 'bg-sky-50 text-sky-700 ring-sky-200',
    },
    {
        value: 'completed',
        label: 'Completed',
        dot: '#10b981',
        badge: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    },
];

export const CATEGORY_COLORS = {
    School: 'from-brand-500 to-brand-700',
    Personal: 'from-sky-400 to-sky-600',
    Organization: 'from-amber-400 to-amber-600',
    Health: 'from-emerald-400 to-emerald-600',
    Other: 'from-violet-400 to-violet-600',
};

export const SORT_OPTIONS = [
    { value: 'due_date', label: 'Due date (soonest)' },
    { value: 'due_date_desc', label: 'Due date (latest)' },
    { value: 'newest', label: 'Newest first' },
    { value: 'oldest', label: 'Oldest first' },
    { value: 'title', label: 'Title A-Z' },
];

export function priorityMeta(value) {
    return PRIORITIES.find((priority) => priority.value === value) ?? PRIORITIES[1];
}

export function statusMeta(value) {
    return STATUSES.find((status) => status.value === value) ?? STATUSES[0];
}

export function formatDate(dateString, options = {}) {
    if (!dateString) {
        return 'No due date';
    }

    const date = new Date(`${dateString}T00:00:00`);

    if (Number.isNaN(date.getTime())) {
        return dateString;
    }

    return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        ...options,
    }).format(date);
}

export function formatTime(timeString) {
    if (!timeString) {
        return '';
    }

    const [hours, minutes] = timeString.split(':').map(Number);

    if (Number.isNaN(hours) || Number.isNaN(minutes)) {
        return timeString;
    }

    return new Intl.DateTimeFormat('en-US', {
        hour: 'numeric',
        minute: '2-digit',
    }).format(new Date(2000, 0, 1, hours, minutes));
}

export function todayISO() {
    const now = new Date();
    const offset = now.getTimezoneOffset();
    return new Date(now.getTime() - offset * 60 * 1000).toISOString().slice(0, 10);
}

export function greeting() {
    const hour = new Date().getHours();

    if (hour < 12) {
        return 'Good morning';
    }

    if (hour < 18) {
        return 'Good afternoon';
    }

    return 'Good evening';
}
