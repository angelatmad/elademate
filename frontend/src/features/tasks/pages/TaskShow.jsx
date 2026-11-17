import { useEffect, useState } from 'react';
import { Link, useLocation, useNavigate, useParams } from 'react-router-dom';
import {
    ArrowLeft,
    CalendarDays,
    Check,
    Clock3,
    Pencil,
    Tag,
    Trash2,
} from 'lucide-react';
import Alert from '@/components/ui/Alert';
import ConfirmDialog from '@/components/ui/ConfirmDialog';
import PageHeader from '@/components/ui/PageHeader';
import { CardSkeleton, Spinner } from '@/components/ui/Loading';
import { CategoryBadge, PriorityBadge, StatusBadge } from '@/components/ui/Badge';
import api, { getApiError } from '@/services/api/client';
import { formatDate, formatTime } from '@/utils/taskMeta';

export default function TaskShow() {
    const { id } = useParams();
    const navigate = useNavigate();
    const location = useLocation();
    const [task, setTask] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState(location.state?.message || '');
    const [confirmOpen, setConfirmOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [toggling, setToggling] = useState(false);

    useEffect(() => {
        let active = true;

        api.get(`/tasks/${id}`)
            .then((response) => {
                if (active) {
                    setTask(response.data.data);
                }
            })
            .catch((requestError) => {
                if (active) {
                    setError(getApiError(requestError, 'We could not load this task.').message);
                }
            })
            .finally(() => {
                if (active) {
                    setLoading(false);
                }
            });

        return () => {
            active = false;
        };
    }, [id]);

    const handleToggle = async () => {
        setToggling(true);
        setError('');

        try {
            const response = await api.patch(`/tasks/${id}/toggle`);
            setTask(response.data.data);
            setSuccess(response.data.message);
        } catch (requestError) {
            setError(getApiError(requestError, 'We could not update this task.').message);
        } finally {
            setToggling(false);
        }
    };

    const handleDelete = async () => {
        setDeleting(true);
        setError('');

        try {
            await api.delete(`/tasks/${id}`);
            navigate('/tasks', { replace: true });
        } catch (requestError) {
            setError(getApiError(requestError, 'We could not delete this task.').message);
            setDeleting(false);
        }
    };

    if (loading) {
        return (
            <div className="space-y-5">
                <div className="skeleton h-16 w-64" />
                <CardSkeleton count={3} />
            </div>
        );
    }

    if (error && !task) {
        return (
            <div className="space-y-4">
                <Alert message={error} />
                <Link to="/tasks" className="btn-secondary">
                    <ArrowLeft className="h-4 w-4" />
                    Back to tasks
                </Link>
            </div>
        );
    }

    if (!task) {
        return null;
    }

    return (
        <div className="space-y-5">
            <PageHeader
                eyebrow="Task details"
                title={task.title}
                subtitle={task.is_overdue ? 'This task is past its due date and needs attention.' : 'A clear view of everything attached to this task.'}
                actions={
                    <>
                        <Link to={`/tasks/${task.id}/edit`} className="btn-secondary">
                            <Pencil className="h-4 w-4" />
                            Edit
                        </Link>
                        <button type="button" onClick={() => setConfirmOpen(true)} className="btn-danger">
                            <Trash2 className="h-4 w-4" />
                            Delete
                        </button>
                    </>
                }
                back
                backTo="/tasks"
            />

            <Alert message={success} type="success" onClose={() => setSuccess('')} />
            <Alert message={error} onClose={() => setError('')} />

            <div className="grid gap-5 lg:grid-cols-[1.4fr_1fr]">
                <section className="card p-5 sm:p-6">
                    <div className="flex flex-wrap items-center gap-2">
                        <PriorityBadge value={task.priority} />
                        <StatusBadge value={task.status} />
                        <CategoryBadge category={task.category} />
                        {task.is_overdue && (
                            <span className="badge bg-rose-50 text-rose-700 ring-rose-200">Overdue</span>
                        )}
                    </div>

                    <h2 className="mt-6 text-sm font-semibold text-ink-900">Description</h2>
                    <p className="mt-2 whitespace-pre-line text-sm leading-relaxed text-ink-500">
                        {task.description || 'No extra notes were added to this task.'}
                    </p>

                    <div className="mt-6 rounded-xl border border-brand-100 bg-brand-50/50 p-4">
                        <button
                            type="button"
                            onClick={handleToggle}
                            disabled={toggling}
                            className={task.is_completed ? 'btn-secondary' : 'btn-primary'}
                        >
                            {toggling ? <Spinner className="h-4 w-4" /> : <Check className="h-4 w-4" />}
                            {task.is_completed ? 'Mark as active again' : 'Mark as completed'}
                        </button>
                        <p className="mt-2 text-[11px] text-ink-500">
                            {task.is_completed
                                ? 'This task is complete. Toggling it will put it back in your active list.'
                                : 'Completing a task updates your dashboard progress instantly.'}
                        </p>
                    </div>
                </section>

                <section className="card p-5 sm:p-6">
                    <h2 className="text-sm font-semibold text-ink-900">Schedule</h2>
                    <div className="mt-4 space-y-4 text-sm">
                        <div className="flex items-start gap-3">
                            <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                                <CalendarDays className="h-4 w-4" />
                            </span>
                            <div>
                                <p className="text-[11px] font-semibold uppercase tracking-[0.14em] text-ink-500">Due date</p>
                                <p className={`mt-1 font-medium ${task.is_overdue ? 'text-rose-600' : 'text-ink-900'}`}>
                                    {task.due_date ? formatDate(task.due_date, { weekday: 'short' }) : 'No due date'}
                                </p>
                            </div>
                        </div>

                        <div className="flex items-start gap-3">
                            <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                                <Clock3 className="h-4 w-4" />
                            </span>
                            <div>
                                <p className="text-[11px] font-semibold uppercase tracking-[0.14em] text-ink-500">Due time</p>
                                <p className="mt-1 font-medium text-ink-900">{task.due_time ? formatTime(task.due_time) : 'No specific time'}</p>
                            </div>
                        </div>

                        <div className="flex items-start gap-3">
                            <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                <Tag className="h-4 w-4" />
                            </span>
                            <div>
                                <p className="text-[11px] font-semibold uppercase tracking-[0.14em] text-ink-500">Category</p>
                                <p className="mt-1 font-medium text-ink-900">{task.category || 'Uncategorized'}</p>
                            </div>
                        </div>
                    </div>

                    <div className="mt-6 border-t border-brand-50 pt-4 text-[11px] text-ink-500">
                        <p>Created {new Date(task.created_at).toLocaleString()}</p>
                        <p className="mt-1">Updated {new Date(task.updated_at).toLocaleString()}</p>
                    </div>
                </section>
            </div>

            <ConfirmDialog
                open={confirmOpen}
                busy={deleting}
                message={`"${task.title}" will be permanently removed. This action cannot be undone.`}
                onCancel={() => setConfirmOpen(false)}
                onConfirm={handleDelete}
            />
        </div>
    );
}
