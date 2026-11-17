import { CheckCircle2, CircleAlert, Info, X } from 'lucide-react';

const variants = {
    success: { classes: 'border-emerald-200 bg-emerald-50 text-emerald-800', Icon: CheckCircle2, role: 'status' },
    info: { classes: 'border-sky-200 bg-sky-50 text-sky-800', Icon: Info, role: 'status' },
    error: { classes: 'border-rose-200 bg-rose-50 text-rose-800', Icon: CircleAlert, role: 'alert' },
};

export default function Alert({ type = 'error', message, onClose, className = '' }) {
    if (!message) {
        return null;
    }

    const { classes, Icon, role } = variants[type] ?? variants.error;

    return (
        <div
            className={`flex items-start gap-3 rounded-xl border px-4 py-3 text-sm ${classes} ${className}`}
            role={role}
        >
            <Icon className="mt-0.5 h-4 w-4 shrink-0" aria-hidden="true" />
            <p className="min-w-0 flex-1 leading-relaxed">{message}</p>
            {onClose && (
                <button
                    type="button"
                    onClick={onClose}
                    className="rounded-md p-0.5 opacity-60 transition hover:opacity-100"
                    aria-label="Dismiss message"
                >
                    <X className="h-4 w-4" />
                </button>
            )}
        </div>
    );
}
