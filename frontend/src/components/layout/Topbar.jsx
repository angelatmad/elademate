import { useEffect, useRef, useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { Bell, Menu, Search } from 'lucide-react';
import Avatar from '@/components/ui/Avatar';
import { useAuth } from '@/features/auth/hooks/useAuth';
import api from '@/services/api/client';
import { formatDate, formatTime } from '@/utils/taskMeta';

export default function Topbar({ onOpenSidebar }) {
    const { user } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();
    const searchParams = new URLSearchParams(location.search);

    const [query, setQuery] = useState(searchParams.get('q') || '');
    const [notifications, setNotifications] = useState([]);
    const [notificationCount, setNotificationCount] = useState(0);
    const [notificationsOpen, setNotificationsOpen] = useState(false);
    const notificationsRef = useRef(null);

    useEffect(() => {
        setQuery(searchParams.get('q') || '');
    }, [location.search]);

    useEffect(() => {
        let active = true;
        const loadNotifications = () => {
            api.get('/notifications')
                .then((response) => {
                    if (active) {
                        setNotifications(response.data.data || []);
                        setNotificationCount(response.data.meta?.count || 0);
                    }
                })
                .catch(() => {});
        };

        loadNotifications();
        const timer = window.setInterval(loadNotifications, 60000);
        return () => {
            active = false;
            window.clearInterval(timer);
        };
    }, []);

    useEffect(() => {
        const handleClick = (event) => {
            if (notificationsRef.current && !notificationsRef.current.contains(event.target)) {
                setNotificationsOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClick);
        return () => document.removeEventListener('mousedown', handleClick);
    }, []);

    const handleSearch = (event) => {
        event.preventDefault();
        const term = query.trim();
        navigate(term ? `/tasks?q=${encodeURIComponent(term)}` : '/tasks');
    };

    return (
        <header className="sticky top-0 z-30 border-b border-[#F8DCE6] bg-white">
            <div className="flex h-14 items-center gap-2 px-3 sm:gap-3 sm:px-6 lg:px-8">
                <button type="button" className="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-[#D4145A] hover:bg-[#F8DCE6]/50 md:hidden" onClick={onOpenSidebar} aria-label="Open navigation">
                    <Menu className="h-5 w-5" />
                </button>

                <form onSubmit={handleSearch} className="relative min-w-0 flex-1 max-w-md" role="search">
                    <label htmlFor="global-search" className="sr-only">Search tasks</label>
                    <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input
                        id="global-search"
                        type="search"
                        value={query}
                        onChange={(event) => setQuery(event.target.value)}
                        placeholder="Search tasks"
                        className="w-full rounded-full border border-[#F8DCE6] bg-white py-1.5 pl-9 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-[#D4145A] focus:outline-none focus:ring-1 focus:ring-[#D4145A]"
                    />
                </form>

                <div className="ml-auto flex shrink-0 items-center gap-1 sm:gap-2">
                    <div className="relative" ref={notificationsRef}>
                        <button
                            type="button"
                            onClick={() => setNotificationsOpen((v) => !v)}
                            className="relative inline-flex h-9 w-9 items-center justify-center rounded-full text-[#D4145A] hover:bg-[#F8DCE6]/50"
                            aria-label="Notifications"
                            aria-expanded={notificationsOpen}
                        >
                            <Bell className="h-5 w-5" />
                            {notificationCount > 0 && (
                                <span className="absolute right-0.5 top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-[#D4145A] px-1 text-[9px] font-bold text-white">
                                    {notificationCount}
                                </span>
                            )}
                        </button>

                        {notificationsOpen && (
                            <div className="fixed left-3 right-3 top-14 z-50 mt-2 overflow-hidden rounded-xl border border-[#F8DCE6] bg-white shadow-lg sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:z-40 sm:w-[min(320px,calc(100vw-2rem))]">
                                <div className="flex items-center justify-between border-b border-[#F8DCE6] px-3 py-2">
                                    <p className="text-sm font-semibold text-gray-900">Due soon</p>
                                    <span className="text-xs text-[#D4145A]">{notificationCount}</span>
                                </div>
                                <div className="max-h-72 overflow-y-auto">
                                    {notifications.length === 0 ? (
                                        <p className="px-3 py-4 text-center text-xs text-gray-500">Nothing due soon.</p>
                                    ) : (
                                        notifications.map((task) => (
                                            <Link key={task.id} to={`/tasks/${task.id}`} onClick={() => setNotificationsOpen(false)} className="block border-b border-[#F8DCE6]/60 px-3 py-2 last:border-0 hover:bg-[#F8DCE6]/30">
                                                <p className="truncate text-sm font-medium text-gray-900">{task.title}</p>
                                                <p className={`text-[11px] ${task.is_overdue ? 'text-rose-600' : 'text-gray-500'}`}>
                                                    {task.is_overdue ? 'Overdue' : 'Due'} {formatDate(task.due_date, { year: undefined })}{task.due_time ? ` at ${formatTime(task.due_time)}` : ''}
                                                </p>
                                            </Link>
                                        ))
                                    )}
                                </div>
                            </div>
                        )}
                    </div>

                    <Link to="/settings" className="rounded-full ring-1 ring-[#F8DCE6]" aria-label="Profile">
                        <Avatar user={user} size="sm" />
                    </Link>
                </div>
            </div>
        </header>
    );
}
