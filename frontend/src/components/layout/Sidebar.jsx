import { NavLink, useNavigate } from 'react-router-dom';
import { CalendarDays, LayoutDashboard, ListChecks, LogOut, Settings, X } from 'lucide-react';
import Logo from '@/components/ui/Logo';
import { useAuth } from '@/features/auth/hooks/useAuth';

const links = [
    { to: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
    { to: '/tasks', label: 'My Task', icon: ListChecks },
    { to: '/calendar', label: 'Calendar', icon: CalendarDays },
    { to: '/settings', label: 'Settings', icon: Settings },
];

export default function Sidebar({ open, onClose }) {
    const { logout } = useAuth();
    const navigate = useNavigate();

    const handleLogout = async () => {
        try {
            await logout();
        } finally {
            navigate('/login', { replace: true });
        }
    };

    return (
        <>
            <div
                className={`fixed inset-0 z-40 bg-black/20 transition-opacity md:hidden ${open ? 'opacity-100' : 'pointer-events-none opacity-0'}`}
                onClick={onClose}
                aria-hidden="true"
            />

            <aside
                className={`fixed inset-y-0 left-0 z-50 flex w-[220px] flex-col border-r border-[#F8DCE6] bg-[#F8DCE6] transition-transform duration-300 md:translate-x-0 ${open ? 'translate-x-0' : '-translate-x-full'}`}
                aria-label="Main sidebar"
            >
                <div className="flex items-center justify-between px-4 pt-5 pb-4">
                    <Logo to="/dashboard" size="h-10" showText className="mx-auto" />
                    <button type="button" className="ml-2 inline-flex h-8 w-8 items-center justify-center rounded-md text-[#B80F4A] hover:bg-white/50 md:hidden" onClick={onClose} aria-label="Close navigation">
                        <X className="h-4 w-4" />
                    </button>
                </div>

                <nav className="flex-1 space-y-1 px-3 pt-2" aria-label="Main navigation">
                    {links.map(({ to, label, icon: Icon }) => (
                        <NavLink
                            key={to}
                            to={to}
                            onClick={onClose}
                            className={({ isActive }) =>
                                `flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-200 ${
                                    isActive
                                        ? 'bg-gradient-to-r from-[#F52D70] to-[#D4145A] text-white shadow-sm'
                                        : 'text-[#7a4b5e] hover:bg-white/50 hover:text-[#B80F4A]'
                                }`
                            }
                        >
                            <Icon className="h-4 w-4 shrink-0" />
                            <span>{label}</span>
                        </NavLink>
                    ))}
                </nav>

                <div className="border-t border-[#f0cbd9] p-3">
                    <button
                        type="button"
                        onClick={handleLogout}
                        className="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-[#7a4b5e] transition hover:bg-white/40 hover:text-[#B80F4A]"
                    >
                        <LogOut className="h-4 w-4" />
                        <span>Logout</span>
                    </button>
                </div>
            </aside>
        </>
    );
}
