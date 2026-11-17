import { useCallback, useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { CalendarDays, Check, CheckCircle2, Clock3, ListChecks, MoreHorizontal } from 'lucide-react';
import Alert from '@/components/ui/Alert';
import Avatar from '@/components/ui/Avatar';
import FloatingAddTask from '@/components/ui/FloatingAddTask';
import StatCard from '@/components/ui/StatCard';
import { DashboardSkeleton } from '@/components/ui/Loading';
import { useAuth } from '@/features/auth/hooks/useAuth';
import api, { getApiError } from '@/services/api/client';
import { formatDate, formatTime, greeting, priorityMeta } from '@/utils/taskMeta';

function TodayTaskCard({ task, onToggle, toggling }) {
    const p = priorityMeta(task.priority);
    const completed = task.is_completed || task.status === 'completed';
    return (
        <div className="flex items-center gap-3 rounded-xl border border-[#F8DCE6] bg-white px-3 py-2.5">
            <button
                type="button"
                onClick={() => onToggle(task)}
                disabled={toggling}
                className={`flex h-5 w-5 shrink-0 items-center justify-center rounded border transition ${
                    completed ? 'border-[#D4145A] bg-[#D4145A] text-white' : 'border-[#D4145A]/50 bg-white text-transparent hover:border-[#D4145A]'
                } ${toggling ? 'opacity-60' : ''}`}
                aria-label={completed ? 'Mark as not completed' : 'Mark as completed'}
            >
                <Check className="h-3 w-3" strokeWidth={3} />
            </button>

            <div className="min-w-0 flex-1">
                <Link to={`/tasks/${task.id}`} className={`block truncate text-sm font-medium ${completed ? 'text-gray-400 line-through' : 'text-gray-900 hover:text-[#D4145A]'}`}>
                    {task.title}
                </Link>
                <div className="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px] text-gray-500">
                    {task.category && <span>{task.category}</span>}
                    <span>{task.due_date ? formatDate(task.due_date, { year: undefined }) : 'Today'}</span>
                    {task.due_time && <span className="inline-flex items-center gap-0.5"><Clock3 className="h-3 w-3" />{formatTime(task.due_time)}</span>}
                </div>
            </div>

            <span className={`hidden shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold capitalize sm:inline ${p.badge}`}>{p.label}</span>
            <Link to={`/tasks/${task.id}`} className="shrink-0 text-gray-400 hover:text-[#D4145A]" aria-label={`Open ${task.title}`}>
                <MoreHorizontal className="h-4 w-4" />
            </Link>
        </div>
    );
}

function DeadlineCard({ task }) {
    const p = priorityMeta(task.priority);
    const completed = task.is_completed || task.status === 'completed';
    const statusLabel = completed ? 'Completed' : task.is_overdue ? 'Overdue' : 'Upcoming';
    const statusClass = completed ? 'bg-emerald-50 text-emerald-700' : task.is_overdue ? 'bg-rose-50 text-rose-600' : 'bg-[#FDF2F6] text-[#B80F4A]';
    return (
        <Link to={`/tasks/${task.id}`} className="flex items-center gap-3 rounded-xl border border-[#F8DCE6] bg-white px-3 py-2.5 transition hover:border-[#f0b6cc] hover:bg-[#FDF2F6]/30">
            <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#FDF2F6] text-[#D4145A]">
                <CalendarDays className="h-4 w-4" />
            </span>
            <span className="min-w-0 flex-1">
                <span className={`block truncate text-sm font-medium ${completed ? 'text-gray-400 line-through' : 'text-gray-900'}`}>{task.title}</span>
                <span className="mt-0.5 block text-[11px] text-gray-500">
                    {task.category ? `${task.category} · ` : ''}{formatDate(task.due_date, { year: undefined })}{task.due_time ? `, ${formatTime(task.due_time)}` : ''}
                </span>
            </span>
            <span className="flex shrink-0 flex-col items-end gap-1">
                <span className={`rounded-full px-2 py-0.5 text-[10px] font-semibold capitalize ${p.badge}`}>{p.label}</span>
                <span className={`rounded-full px-2 py-0.5 text-[10px] font-medium ${statusClass}`}>{statusLabel}</span>
            </span>
        </Link>
    );
}

export default function Dashboard() {
    const { user } = useAuth();
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [togglingId, setTogglingId] = useState(null);

    const load = useCallback(async (showLoader = true) => {
        if (showLoader) setLoading(true);
        setError('');
        try {
            const response = await api.get('/dashboard');
            setData(response.data.data);
        } catch (requestError) {
            setError(getApiError(requestError, 'We could not load your dashboard.').message);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => { load(); }, [load]);

    const handleToggle = async (task) => {
        setTogglingId(task.id);
        try {
            await api.patch(`/tasks/${task.id}/toggle`);
            await load(false);
        } catch (requestError) {
            setError(getApiError(requestError, 'We could not update that task.').message);
        } finally {
            setTogglingId(null);
        }
    };

    if (loading) return <DashboardSkeleton />;

    if (error || !data) {
        return (
            <div className="space-y-4">
                <Alert message={error || 'Dashboard data is unavailable.'} />
                <button type="button" onClick={() => load()} className="btn-primary">Try again</button>
            </div>
        );
    }

    const { stats, today_tasks: todayTasks = [], upcoming_tasks: upcomingTasks = [] } = data;
    const firstName = user?.first_name || user?.name?.split(' ')[0] || 'Student';

    return (
        <div className="space-y-4 pb-24 sm:pb-20">
            {error && <Alert message={error} onClose={() => setError('')} />}

            <section className="flex items-center justify-between gap-3 rounded-xl bg-[#F8DCE6] px-4 py-4 sm:px-5">
                <div className="min-w-0">
                    <p className="text-sm text-[#7a4b5e]">{greeting()},</p>
                    <h1 className="truncate text-2xl font-bold leading-tight text-[#D4145A]">{firstName}!</h1>
                    <p className="mt-1 text-xs text-[#7a4b5e]">Keep going! You&apos;re doing great.</p>
                </div>
                <Avatar user={user} size="xl" className="ring-2 ring-white" />
            </section>

            <section className="grid w-full grid-cols-1 gap-3 md:grid-cols-3">
                <StatCard icon={ListChecks} label="All Tasks" value={stats.total} />
                <StatCard icon={Clock3} label="Pending" value={stats.pending} />
                <StatCard icon={CheckCircle2} label="Completed" value={stats.completed} />
            </section>

            <section className="rounded-xl border border-[#F8DCE6] bg-white px-4 py-3">
                <div className="flex items-center justify-between gap-3">
                    <div className="min-w-0">
                        <p className="text-sm font-semibold text-gray-900">Overall Progress</p>
                        <p className="text-[11px] text-gray-500">{stats.completed} of {stats.total} tasks completed</p>
                    </div>
                    <span className="shrink-0 text-sm font-semibold text-[#D4145A]">{stats.progress}%</span>
                </div>
                <div className="mt-2 h-2 w-full overflow-hidden rounded-full bg-[#F8DCE6]">
                    <div className="h-full rounded-full bg-[#D4145A] transition-all" style={{ width: `${stats.progress}%` }} />
                </div>
            </section>

            <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <section className="rounded-xl border border-[#F8DCE6] bg-white p-4">
                    <div className="mb-3 flex items-center justify-between">
                        <h2 className="text-sm font-semibold text-gray-900">Today&apos;s Tasks</h2>
                        <Link to="/tasks" className="text-xs font-medium text-[#D4145A] hover:underline">View all</Link>
                    </div>
                    <div className="space-y-2">
                        {todayTasks.length === 0 ? (
                            <p className="rounded-lg bg-[#FDF2F6] px-3 py-6 text-center text-xs text-gray-500">No tasks due today. You&apos;re all caught up!</p>
                        ) : (
                            todayTasks.map((task) => (
                                <TodayTaskCard key={task.id} task={task} onToggle={handleToggle} toggling={togglingId === task.id} />
                            ))
                        )}
                    </div>
                </section>

                <section className="rounded-xl border border-[#F8DCE6] bg-white p-4">
                    <div className="mb-3 flex items-center justify-between">
                        <h2 className="text-sm font-semibold text-gray-900">Upcoming Deadlines</h2>
                        <Link to="/tasks?status=pending" className="text-xs font-medium text-[#D4145A] hover:underline">View all</Link>
                    </div>
                    <div className="space-y-2">
                        {upcomingTasks.length === 0 ? (
                            <p className="rounded-lg bg-[#FDF2F6] px-3 py-6 text-center text-xs text-gray-500">No upcoming deadlines. Add a task with a future due date.</p>
                        ) : (
                            upcomingTasks.map((task) => <DeadlineCard key={task.id} task={task} />)
                        )}
                    </div>
                </section>
            </div>

            <FloatingAddTask />
        </div>
    );
}