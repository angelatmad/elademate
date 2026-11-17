import { Link } from 'react-router-dom';
import { Sparkles } from 'lucide-react';

export default function EmptyState({
    icon: Icon = Sparkles,
    title,
    message,
    actionLabel,
    actionTo,
    onAction,
    children,
}) {
    return (
        <div className="card flex flex-col items-center px-6 py-12 text-center">
            <span className="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                <Icon className="h-6 w-6" aria-hidden="true" />
            </span>
            <h2 className="mt-4 text-base font-semibold text-ink-900">{title}</h2>
            {message && <p className="mt-1.5 max-w-md text-sm leading-relaxed text-ink-500">{message}</p>}
            {actionLabel && actionTo && (
                <Link to={actionTo} className="btn-primary mt-5">
                    {actionLabel}
                </Link>
            )}
            {actionLabel && onAction && (
                <button type="button" onClick={onAction} className="btn-primary mt-5">
                    {actionLabel}
                </button>
            )}
            {children}
        </div>
    );
}
