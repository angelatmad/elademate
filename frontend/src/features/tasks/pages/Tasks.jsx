import { useCallback, useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import Alert from '@/components/ui/Alert';
import FloatingAddTask from '@/components/ui/FloatingAddTask';
import EmptyState from '@/components/ui/EmptyState';
import TaskCard from '@/features/tasks/components/TaskCard';
import { CardSkeleton } from '@/components/ui/Loading';
import api, { getApiError } from '@/services/api/client';

const tabs = [
    { key: '', label: 'All', countKey: 'all' },
    { key: 'pending', label: 'Pending', countKey: 'pending' },
    { key: 'completed', label: 'Completed', countKey: 'completed' },
    { key: 'overdue', label: 'Overdue', countKey: 'overdue' },
];

const defaultCounts = { all: 0, pending: 0, in_progress: 0, completed: 0, overdue: 0 };

export default function Tasks() {
    const [searchParams, setSearchParams] = useSearchParams();
    const queryString = searchParams.toString();
    const status = searchParams.get('status') || '';

    const [tasks, setTasks] = useState([]);
    const [meta, setMeta] = useState(null);
    const [counts, setCounts] = useState(defaultCounts);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    const load = useCallback(async (showLoader = true) => {
        if (showLoader) setLoading(true);
        setError('');
        try {
            const response = await api.get(`/tasks${queryString ? `?${queryString}` : ''}`);
            setTasks(response.data.data || []);
            setMeta(response.data.meta || null);
            setCounts({ ...defaultCounts, ...(response.data.counts || {}) });
        } catch (requestError) {
            setError(getApiError(requestError, 'We could not load your tasks.').message);
        } finally {
            setLoading(false);
        }
    }, [queryString]);

    useEffect(() => { load(); }, [load]);

    const updateStatus = (key) => {
        const next = new URLSearchParams(searchParams);
        if (key) next.set('status', key); else next.delete('status');
        next.delete('page');
        setSearchParams(next);
    };

    const handleToggle = async (task) => {
        try {
            await api.patch(`/tasks/${task.id}/toggle`);
            await load(false);
        } catch (requestError) {
            setError(getApiError(requestError, 'We could not update that task.').message);
        }
    };

    const handleDelete = async (task) => {
        try {
            await api.delete(`/tasks/${task.id}`);
            await load(false);
        } catch (requestError) {
            setError(getApiError(requestError, 'We could not delete that task.').message);
            throw requestError;
        }
    };

    const totalCount = counts.all ?? 0;
    const completedCount = counts.completed ?? 0;
    const currentDate = new Intl.DateTimeFormat('en-US', { month: 'long', day: 'numeric', year: 'numeric' }).format(new Date());
    const hasQuery = Boolean(searchParams.get('q'));

    return (
        <div className="space-y-4 pb-24 sm:pb-20">
            <div>
                <h1 className="text-2xl font-semibold text-gray-900">My Tasks</h1>
                <p className="mt-0.5 text-sm text-gray-500">Keep track of your tasks, one deadline at a time.</p>
            </div>

            <div className="flex gap-2 overflow-x-auto pb-1" role="tablist" aria-label="Filter tasks">
                {tabs.map((tab) => {
                    const active = status === tab.key;
                    return (
                        <button
                            key={tab.key || 'all'}
                            type="button"
                            role="tab"
                            aria-selected={active}
                            onClick={() => updateStatus(tab.key)}
                            className={`inline-flex shrink-0 items-center gap-2 rounded-lg border px-3 py-1.5 text-sm font-medium transition ${
                                active
                                    ? 'border-[#D4145A] bg-[#D4145A] text-white'
                                    : 'border-[#F8DCE6] bg-white text-gray-600 hover:border-[#f0b6cc] hover:bg-[#FDF2F6]'
                            }`}
                        >
                            {tab.label}
                            <span className={`rounded-full px-1.5 py-0.5 text-[11px] font-semibold ${active ? 'bg-white/25 text-white' : 'bg-[#F8DCE6] text-[#D4145A]'}`}>
                                {counts[tab.countKey] ?? 0}
                            </span>
                        </button>
                    );
                })}
            </div>

            <div className="flex items-center justify-between gap-3 text-xs text-gray-500">
                <span>All Tasks: <span className="font-medium text-gray-700">{totalCount}</span></span>
                <span>{currentDate}</span>
            </div>

            {error && <Alert message={error} onClose={() => setError('')} />}

            {loading ? (
                <CardSkeleton count={4} />
            ) : tasks.length === 0 ? (
                <EmptyState
                    title={hasQuery || status ? 'No tasks found' : 'No tasks yet'}
                    message={hasQuery || status ? 'Try a different search or filter.' : 'Add your first task to get started.'}
                    actionLabel={hasQuery || status ? undefined : 'Add a task'}
                    actionTo={hasQuery || status ? undefined : '/tasks/new'}
                />
            ) : (
                <div className="space-y-2">
                    {tasks.map((task) => (
                        <TaskCard key={task.id} task={task} onToggle={handleToggle} onDelete={handleDelete} />
                    ))}
                </div>
            )}

            {meta && meta.last_page > 1 && (
                <div className="flex items-center justify-center gap-2 pt-1">
                    <button type="button" disabled={meta.current_page <= 1} onClick={() => { const n = new URLSearchParams(searchParams); n.set('page', String(meta.current_page - 1)); setSearchParams(n); }} className="rounded-md border border-[#F8DCE6] px-3 py-1 text-xs text-gray-600 disabled:opacity-40">Prev</button>
                    <span className="text-xs text-gray-500">Page {meta.current_page} of {meta.last_page}</span>
                    <button type="button" disabled={meta.current_page >= meta.last_page} onClick={() => { const n = new URLSearchParams(searchParams); n.set('page', String(meta.current_page + 1)); setSearchParams(n); }} className="rounded-md border border-[#F8DCE6] px-3 py-1 text-xs text-gray-600 disabled:opacity-40">Next</button>
                </div>
            )}

            <p className="pt-2 text-center text-xs text-gray-500">
                Check a task when it&apos;s done. You&apos;ve completed {completedCount} of {totalCount} tasks.
            </p>

            <FloatingAddTask />
        </div>
    );
}