import { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import { Check, MoreHorizontal, Pencil, Trash2 } from 'lucide-react';
import ConfirmDialog from '@/components/ui/ConfirmDialog';
import { formatDate, formatTime, priorityMeta } from '@/utils/taskMeta';

function displayStatus(task) {
    if (task.is_completed || task.status === 'completed') return 'Completed';
    if (task.is_overdue) return 'Overdue';
    return 'Pending';
}

const statusStyles = {
    Pending: 'bg-[#F8DCE6] text-[#B80F4A]',
    Completed: 'bg-emerald-50 text-emerald-700',
    Overdue: 'bg-rose-50 text-rose-600',
};

export default function TaskCard({ task, onToggle, onDelete }) {
    const [menuOpen, setMenuOpen] = useState(false);
    const [confirmOpen, setConfirmOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [toggling, setToggling] = useState(false);
    const menuRef = useRef(null);

    useEffect(() => {
        if (!menuOpen) return;
        const onClick = (e) => {
            if (menuRef.current && !menuRef.current.contains(e.target)) setMenuOpen(false);
        };
        document.addEventListener('mousedown', onClick);
        return () => document.removeEventListener('mousedown', onClick);
    }, [menuOpen]);

    const handleToggle = async () => {
        if (!onToggle || toggling) return;
        setToggling(true);
        try { await onToggle(task); } finally { setToggling(false); }
    };

    const handleDelete = async () => {
        if (!onDelete) return;
        setDeleting(true);
        try { await onDelete(task); setConfirmOpen(false); } finally { setDeleting(false); }
    };

    const status = displayStatus(task);
    const priority = priorityMeta(task.priority);

    return (
        <article className="rounded-lg border border-[#F8DCE6] bg-white px-3 py-2.5">
            <div className="flex items-start gap-3 sm:items-center">
                <button
                    type="button"
                    onClick={handleToggle}
                    disabled={toggling}
                    className={`mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded border transition sm:mt-0 ${
                        task.is_completed ? 'border-[#D4145A] bg-[#D4145A] text-white' : 'border-[#D4145A]/50 bg-white text-transparent hover:border-[#D4145A]'
                    } ${toggling ? 'opacity-60' : ''}`}
                    aria-label={task.is_completed ? 'Mark as not completed' : 'Mark as completed'}
                >
                    <Check className="h-3 w-3" strokeWidth={3} />
                </button>

                <div className="min-w-0 flex-1">
                    <Link to={`/tasks/${task.id}`} className={`block truncate text-sm font-medium ${task.is_completed ? 'text-gray-400 line-through' : 'text-gray-900 hover:text-[#D4145A]'}`}>
                        {task.title}
                    </Link>
                    <div className="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[11px] text-gray-500">
                        <span>{task.category || 'Uncategorized'}</span>
                        <span>
                            {task.due_date ? formatDate(task.due_date, { year: undefined }) : 'No due date'}
                            {task.due_time ? ` · ${formatTime(task.due_time)}` : ''}
                        </span>
                    </div>
                    <div className="mt-1.5 flex flex-wrap items-center gap-2 sm:hidden">
                        <span className={`rounded-full px-2 py-0.5 text-[10px] font-medium ${statusStyles[status]}`}>{status}</span>
                        <span className={`rounded-full px-2 py-0.5 text-[10px] font-semibold capitalize ${priority.badge}`}>{priority.label}</span>
                    </div>
                </div>

                <div className="hidden items-center gap-2 sm:flex">
                    <span className={`rounded-full px-2 py-0.5 text-[10px] font-medium ${statusStyles[status]}`}>{status}</span>
                    <span className={`rounded-full px-2 py-0.5 text-[10px] font-semibold capitalize ${priority.badge}`}>{priority.label}</span>
                </div>

                <div className="relative shrink-0" ref={menuRef}>
                    <button
                        type="button"
                        onClick={() => setMenuOpen((v) => !v)}
                        className="inline-flex h-8 w-8 items-center justify-center rounded-md text-gray-400 hover:bg-[#FDF2F6] hover:text-[#D4145A]"
                        aria-label={`Actions for ${task.title}`}
                        aria-expanded={menuOpen}
                    >
                        <MoreHorizontal className="h-4 w-4" />
                    </button>
                    {menuOpen && (
                        <div className="absolute right-0 z-20 mt-1 w-32 overflow-hidden rounded-lg border border-[#F8DCE6] bg-white py-1 shadow-lg">
                            <Link to={`/tasks/${task.id}/edit`} onClick={() => setMenuOpen(false)} className="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-[#FDF2F6] hover:text-[#D4145A]">
                                <Pencil className="h-3.5 w-3.5" />Edit
                            </Link>
                            <button type="button" onClick={() => { setMenuOpen(false); setConfirmOpen(true); }} className="flex w-full items-center gap-2 px-3 py-2 text-sm text-rose-600 hover:bg-rose-50">
                                <Trash2 className="h-3.5 w-3.5" />Delete
                            </button>
                        </div>
                    )}
                </div>
            </div>

            <ConfirmDialog
                open={confirmOpen}
                busy={deleting}
                message={`"${task.title}" will be permanently removed. This action cannot be undone.`}
                onCancel={() => setConfirmOpen(false)}
                onConfirm={handleDelete}
            />
        </article>
    );
}