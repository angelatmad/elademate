import { useCallback, useEffect, useMemo, useState } from 'react';
import { Link } from 'react-router-dom';
import { ArrowUpRight, CalendarDays, ChevronLeft, ChevronRight, Clock3, Plus } from 'lucide-react';
import Alert from '@/components/ui/Alert';
import PageHeader from '@/components/ui/PageHeader';
import FloatingAddTask from '@/components/ui/FloatingAddTask';
import { PriorityBadge } from '@/components/ui/Badge';
import api, { getApiError } from '@/services/api/client';
import { formatDate, formatTime } from '@/utils/taskMeta';

function startOfCurrentMonth() {
    const now = new Date();
    return { month: now.getMonth() + 1, year: now.getFullYear() };
}

function MonthSummary({ data }) {
    const completed = data
        ? data.days.reduce((total, day) => total + day.tasks.filter((task) => task.is_completed).length, 0)
        : 0;
    const total = data?.total_this_month ?? 0;
    const progress = total > 0 ? Math.round((completed / total) * 100) : 0;

    return (
        <section className="card p-5">
            <p className="desktop-grid-label">Month summary</p>
            <h2 className="mt-1 text-base font-semibold text-ink-900">{data?.month_label || 'Calendar'}</h2>
            <div className="mt-4 grid grid-cols-2 gap-3">
                <div className="rounded-xl bg-brand-50 p-3.5">
                    <p className="text-2xl font-bold text-brand-700">{total}</p>
                    <p className="text-[11px] text-ink-500">Tasks this month</p>
                </div>
                <div className="rounded-xl bg-emerald-50 p-3.5">
                    <p className="text-2xl font-bold text-emerald-700">{completed}</p>
                    <p className="text-[11px] text-ink-500">Completed in view</p>
                </div>
            </div>
            <div className="mt-4">
                <div className="flex items-center justify-between text-[11px]">
                    <span className="font-semibold text-ink-700">Completion</span>
                    <span className="text-ink-500">{progress}%</span>
                </div>
                <div className="mt-2 h-2 overflow-hidden rounded-full bg-brand-50">
                    <div className="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-700" style={{ width: `${progress}%` }} />
                </div>
            </div>
            <div className="mt-4 space-y-2 border-t border-brand-50 pt-4 text-xs text-ink-500">
                <p className="flex items-center gap-2"><span className="h-2 w-2 rounded-full bg-brand-500" /> High priority tasks</p>
                <p className="flex items-center gap-2"><span className="h-2 w-2 rounded-full bg-brand-300" /> Regular tasks</p>
                <p className="flex items-center gap-2"><span className="h-2 w-2 rounded-full bg-emerald-400" /> Completed tasks</p>
            </div>
            <Link to="/tasks" className="btn-secondary mt-4 w-full">Open task list</Link>
        </section>
    );
}

export default function CalendarPage() {
    const initial = startOfCurrentMonth();
    const [month, setMonth] = useState(initial.month);
    const [year, setYear] = useState(initial.year);
    const [data, setData] = useState(null);
    const [upcomingTasks, setUpcomingTasks] = useState([]);
    const [selectedDate, setSelectedDate] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    const load = useCallback(async () => {
        setLoading(true);
        setError('');

        try {
            const [calendarResponse, dashboardResponse] = await Promise.all([
                api.get('/calendar', { params: { month, year } }),
                api.get('/dashboard').catch(() => null),
            ]);

            const calendar = calendarResponse.data.data;
            setData(calendar);

            if (dashboardResponse) {
                setUpcomingTasks(dashboardResponse.data.data.upcoming_tasks.slice(0, 5));
            }

            const availableDates = calendar.days.map((day) => day.date);
            const preferred = availableDates.includes(calendar.today)
                ? calendar.today
                : calendar.days.find((day) => day.in_month)?.date;

            setSelectedDate((current) => (current && availableDates.includes(current) ? current : preferred));
        } catch (requestError) {
            setError(getApiError(requestError, 'We could not load your calendar.').message);
        } finally {
            setLoading(false);
        }
    }, [month, year]);

    useEffect(() => {
        load();
    }, [load]);

    const changeMonth = (offset) => {
        const date = new Date(year, month - 1 + offset, 1);
        setMonth(date.getMonth() + 1);
        setYear(date.getFullYear());
        setSelectedDate(null);
    };

    const goToToday = () => {
        const now = startOfCurrentMonth();
        setMonth(now.month);
        setYear(now.year);
        setSelectedDate(null);
    };

    const selectedDay = useMemo(
        () => data?.days.find((day) => day.date === selectedDate) || null,
        [data, selectedDate],
    );

    return (
        <div className="space-y-6 pb-24 sm:pb-20">
            <PageHeader
                eyebrow="Plan ahead"
                title="Calendar"
                subtitle="See every deadline in context, then shape your week around what matters most."
            />

            {error && <Alert message={error} onClose={() => setError('')} />}

            <div className="grid gap-5 2xl:grid-cols-[minmax(0,1fr)_380px]">
                <section className="card overflow-hidden">
                    <div className="flex flex-col gap-3 border-b border-brand-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5 lg:px-6">
                        <div className="flex items-center gap-3">
                            <span className="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                <CalendarDays className="h-5 w-5" />
                            </span>
                            <div>
                                <h2 className="text-lg font-semibold text-ink-900">{data?.month_label || `${month}/${year}`}</h2>
                                <p className="text-[11px] text-ink-500">
                                    {data?.total_this_month ?? 0} task{(data?.total_this_month ?? 0) === 1 ? '' : 's'} scheduled this month
                                </p>
                            </div>
                        </div>

                        <div className="flex items-center gap-2">
                            <button type="button" onClick={goToToday} className="btn-secondary !px-4 !py-2 text-xs">Today</button>
                            <button type="button" onClick={() => changeMonth(-1)} className="icon-button h-10 w-10 border border-brand-100 bg-white" aria-label="Previous month">
                                <ChevronLeft className="h-4 w-4" />
                            </button>
                            <button type="button" onClick={() => changeMonth(1)} className="icon-button h-10 w-10 border border-brand-100 bg-white" aria-label="Next month">
                                <ChevronRight className="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    {loading ? (
                        <div className="grid grid-cols-7 gap-px bg-brand-50 p-px">
                            {Array.from({ length: 42 }).map((_, index) => (
                                <div key={index} className="h-16 animate-pulse bg-white/80 sm:h-24 2xl:h-28" />
                            ))}
                        </div>
                    ) : data ? (
                        <>
                            <div className="grid grid-cols-7 border-b border-brand-50 bg-brand-50/40">
                                {data.weekdays.map((weekday) => (
                                    <div key={weekday} className="py-2.5 text-center text-[10px] font-semibold uppercase tracking-[0.08em] text-ink-500 sm:tracking-[0.14em]">
                                        <span className="sm:hidden">{weekday.slice(0, 1)}</span>
                                        <span className="hidden sm:inline">{weekday}</span>
                                    </div>
                                ))}
                            </div>
                            <div className="grid grid-cols-7">
                                {data.days.map((day) => {
                                    const selected = day.date === selectedDate;
                                    const taskCount = day.tasks.length;

                                    return (
                                        <button
                                            key={day.date}
                                            type="button"
                                            onClick={() => setSelectedDate(day.date)}
                                            className={`flex min-h-16 flex-col border-b border-r border-brand-50 p-1.5 text-left transition last:border-r-0 hover:bg-brand-50/50 sm:min-h-24 sm:p-2.5 2xl:min-h-28 ${
                                                !day.in_month ? 'bg-ink-50/30 text-ink-500/50' : 'bg-white'
                                            } ${selected ? 'relative z-10 ring-2 ring-inset ring-brand-400' : ''}`}
                                            aria-label={`${formatDate(day.date)}${taskCount ? `, ${taskCount} tasks` : ''}`}
                                            aria-pressed={selected}
                                        >
                                            <span
                                                className={`inline-flex h-6 w-6 items-center justify-center rounded-full text-[11px] font-semibold sm:h-7 sm:w-7 ${
                                                    day.is_today
                                                        ? 'bg-brand-600 text-white'
                                                        : selected
                                                            ? 'bg-brand-100 text-brand-800'
                                                            : 'text-ink-700'
                                                }`}
                                            >
                                                {day.day}
                                            </span>

                                            <div className="mt-1 flex flex-wrap gap-0.5 sm:hidden">
                                                {day.tasks.slice(0, 4).map((task) => (
                                                    <span
                                                        key={task.id}
                                                        className={`h-1.5 w-1.5 rounded-full ${
                                                            task.is_completed
                                                                ? 'bg-emerald-400'
                                                                : task.priority === 'high'
                                                                    ? 'bg-rose-500'
                                                                    : 'bg-brand-400'
                                                        }`}
                                                    />
                                                ))}
                                            </div>

                                            <div className="mt-1.5 hidden space-y-1 sm:block">
                                                {day.tasks.slice(0, 2).map((task) => (
                                                    <span
                                                        key={task.id}
                                                        className={`block truncate rounded-md px-1.5 py-1 text-[9px] font-medium leading-tight ${
                                                            task.is_completed
                                                                ? 'bg-emerald-50 text-emerald-700'
                                                                : task.priority === 'high'
                                                                    ? 'bg-rose-50 text-rose-700'
                                                                    : 'bg-brand-50 text-brand-700'
                                                        }`}
                                                    >
                                                        {task.title}
                                                    </span>
                                                ))}
                                                {taskCount > 2 && (
                                                    <span className="block px-1.5 text-[9px] font-semibold text-ink-500">
                                                        +{taskCount - 2} more
                                                    </span>
                                                )}
                                            </div>
                                        </button>
                                    );
                                })}
                            </div>
                        </>
                    ) : null}
                </section>

                <div className="grid gap-5 md:grid-cols-2 2xl:grid-cols-1">
                    <section className="card p-5">
                        <p className="desktop-grid-label">Selected day</p>
                        <h2 className="mt-1 text-base font-semibold text-ink-900">
                            {selectedDate ? formatDate(selectedDate, { weekday: 'long' }) : 'Choose a day'}
                        </h2>

                        <div className="mt-4">
                            {!selectedDay || selectedDay.tasks.length === 0 ? (
                                <div className="rounded-xl bg-brand-50/60 px-4 py-10 text-center">
                                    <CalendarDays className="mx-auto h-6 w-6 text-brand-400" />
                                    <p className="mt-2 text-sm font-medium text-ink-700">Nothing scheduled for this day.</p>
                                    <Link to={`/tasks/new${selectedDate ? `?date=${selectedDate}` : ''}`} className="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700 hover:text-brand-800">
                                        <Plus className="h-3.5 w-3.5" />
                                        Add a task for this day
                                    </Link>
                                </div>
                            ) : (
                                <div className="space-y-2">
                                    {selectedDay.tasks.map((task) => (
                                        <Link
                                            key={task.id}
                                            to={`/tasks/${task.id}`}
                                            className="flex items-start gap-3 rounded-xl border border-brand-100 p-3 transition hover:border-brand-200 hover:bg-brand-50/40"
                                        >
                                            <span className={`mt-1.5 h-2 w-2 shrink-0 rounded-full ${task.is_completed ? 'bg-emerald-400' : task.priority === 'high' ? 'bg-rose-500' : 'bg-brand-400'}`} />
                                            <span className="min-w-0 flex-1">
                                                <span className={`block truncate text-sm font-medium ${task.is_completed ? 'text-ink-500 line-through' : 'text-ink-900'}`}>{task.title}</span>
                                                <span className="mt-1 flex flex-wrap items-center gap-2 text-[11px] text-ink-500">
                                                    {task.due_time && <span>{formatTime(task.due_time)}</span>}
                                                    <span>{task.category || 'Uncategorized'}</span>
                                                </span>
                                            </span>
                                            <PriorityBadge value={task.priority} />
                                        </Link>
                                    ))}
                                </div>
                            )}
                        </div>
                    </section>

                    <section className="card p-5">
                        <div className="flex items-center justify-between gap-3">
                            <div>
                                <p className="desktop-grid-label">Next up</p>
                                <h2 className="mt-1 text-base font-semibold text-ink-900">Upcoming deadlines</h2>
                            </div>
                            <Link to="/tasks" className="text-[11px] font-semibold text-brand-700 hover:text-brand-800">See all</Link>
                        </div>

                        <div className="mt-3 space-y-1">
                            {upcomingTasks.length === 0 ? (
                                <p className="rounded-xl bg-brand-50/60 px-4 py-9 text-center text-xs text-ink-500">
                                    No upcoming deadlines yet.
                                </p>
                            ) : (
                                upcomingTasks.slice(0, 4).map((task) => (
                                    <Link
                                        key={task.id}
                                        to={`/tasks/${task.id}`}
                                        className="flex items-center gap-3 rounded-xl px-1 py-2.5 transition hover:bg-brand-50/60"
                                    >
                                        <span className="flex h-10 w-10 shrink-0 flex-col items-center justify-center rounded-xl bg-brand-50 text-brand-700">
                                            <span className="text-[8px] font-bold uppercase leading-none">
                                                {task.due_date ? new Intl.DateTimeFormat('en-US', { month: 'short' }).format(new Date(`${task.due_date}T00:00:00`)) : '--'}
                                            </span>
                                            <span className="mt-1 text-xs font-bold leading-none">
                                                {task.due_date ? new Intl.DateTimeFormat('en-US', { day: '2-digit' }).format(new Date(`${task.due_date}T00:00:00`)) : '--'}
                                            </span>
                                        </span>
                                        <span className="min-w-0 flex-1">
                                            <span className="block truncate text-sm font-medium text-ink-900">{task.title}</span>
                                            <span className="mt-0.5 flex items-center gap-1 text-[11px] text-ink-500">
                                                <Clock3 className="h-3 w-3" />
                                                {task.due_time ? formatTime(task.due_time) : 'All day'}
                                            </span>
                                        </span>
                                        <ArrowUpRight className="h-3.5 w-3.5 shrink-0 text-brand-300" />
                                    </Link>
                                ))
                            )}
                        </div>
                    </section>

                    <MonthSummary data={data} />
                </div>
            </div>

            <FloatingAddTask to={`/tasks/new${selectedDate ? `?date=${selectedDate}` : ''}`} />
        </div>
    );
}
