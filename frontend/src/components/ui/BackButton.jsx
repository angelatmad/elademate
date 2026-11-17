import { useLocation, useNavigate } from 'react-router-dom';
import { ArrowLeft } from 'lucide-react';

function fallbackPath(pathname) {
    const editMatch = pathname.match(/^\/tasks\/([^/]+)\/edit\/?$/);

    if (editMatch) {
        return `/tasks/${editMatch[1]}`;
    }

    if (pathname.startsWith('/tasks')) {
        return '/tasks';
    }

    if (pathname.startsWith('/calendar')) {
        return '/calendar';
    }

    if (pathname.startsWith('/settings')) {
        return '/settings';
    }

    return '/dashboard';
}

export default function BackButton({ to, label = 'Back', className = '' }) {
    const navigate = useNavigate();
    const location = useLocation();

    const handleBack = () => {
        const historyIndex = window.history.state?.idx;

        if (typeof historyIndex === 'number' && historyIndex > 0) {
            navigate(-1);
            return;
        }

        navigate(to || fallbackPath(location.pathname), { replace: true });
    };

    return (
        <button
            type="button"
            onClick={handleBack}
            className={`btn-secondary shrink-0 justify-center border-brand-200 text-brand-700 hover:border-brand-300 hover:bg-brand-50 hover:text-brand-800 ${className}`}
            aria-label={label}
        >
            <ArrowLeft className="h-4 w-4" />
            {label}
        </button>
    );
}
