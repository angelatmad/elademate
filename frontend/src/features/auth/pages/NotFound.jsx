import { Link } from 'react-router-dom';
import { ArrowLeft, Compass } from 'lucide-react';
import Logo from '@/components/ui/Logo';

export default function NotFound() {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-[#fff8fb] px-6 text-center">
            <Logo to="/" />
            <span className="mt-10 flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                <Compass className="h-7 w-7" />
            </span>
            <p className="mt-5 text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">404</p>
            <h1 className="mt-2 text-3xl font-bold tracking-tight text-ink-900">This page wandered off.</h1>
            <p className="mt-2 max-w-md text-sm leading-relaxed text-ink-500">
                The page you are looking for does not exist, but your tasks are still right where you left them.
            </p>
            <Link to="/dashboard" className="btn-primary mt-6">
                <ArrowLeft className="h-4 w-4" />
                Back to dashboard
            </Link>
        </div>
    );
}
