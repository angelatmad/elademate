import { useEffect } from 'react';
import { createPortal } from 'react-dom';
import { Trash2 } from 'lucide-react';
import { Spinner } from '@/components/ui/Loading';

export default function ConfirmDialog({
    open,
    title = 'Delete this task?',
    message,
    confirmLabel = 'Delete',
    cancelLabel = 'Keep task',
    busy = false,
    onConfirm,
    onCancel,
}) {
    useEffect(() => {
        if (!open) {
            return undefined;
        }

        const handleKeyDown = (event) => {
            if (event.key === 'Escape' && !busy) {
                onCancel();
            }
        };

        document.addEventListener('keydown', handleKeyDown);
        document.body.style.overflow = 'hidden';

        return () => {
            document.removeEventListener('keydown', handleKeyDown);
            document.body.style.overflow = '';
        };
    }, [open, busy, onCancel]);

    if (!open) {
        return null;
    }

    return createPortal(
        <div className="fixed inset-0 z-[90] flex items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true" aria-labelledby="confirm-title">
            <button
                type="button"
                className="absolute inset-0 cursor-default bg-ink-900/45 backdrop-blur-sm"
                onClick={() => !busy && onCancel()}
                aria-label="Close dialog"
            />
            <div className="animate-float-in relative w-full max-w-sm rounded-2xl border border-brand-100 bg-white p-5 shadow-2xl">
                <div className="flex h-11 w-11 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                    <Trash2 className="h-5 w-5" aria-hidden="true" />
                </div>
                <h2 id="confirm-title" className="mt-3 text-base font-semibold text-ink-900">{title}</h2>
                {message && <p className="mt-1 text-sm leading-relaxed text-ink-500">{message}</p>}
                <div className="mt-5 flex justify-end gap-2">
                    <button type="button" className="btn-secondary" onClick={onCancel} disabled={busy}>
                        {cancelLabel}
                    </button>
                    <button type="button" className="btn-danger" onClick={onConfirm} disabled={busy}>
                        {busy && <Spinner className="h-4 w-4" />}
                        {confirmLabel}
                    </button>
                </div>
            </div>
        </div>,
        document.body,
    );
}
