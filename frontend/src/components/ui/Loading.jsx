import Logo from '@/components/ui/Logo';

export function Spinner({ className = 'h-5 w-5' }) {
    return (
        <span
            className={`inline-block animate-spin rounded-full border-2 border-current border-t-transparent ${className}`}
            aria-hidden="true"
        />
    );
}

export function LoadingScreen({ label = 'Preparing your workspace...' }) {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center gap-4 bg-[#fff8fb] px-6">
            <Logo to="/" />
            <div className="flex items-center gap-2 text-sm font-medium text-ink-500">
                <Spinner className="h-4 w-4 text-brand-600" />
                {label}
            </div>
        </div>
    );
}

export function CardSkeleton({ count = 3 }) {
    return (
        <div className="space-y-3" aria-hidden="true">
            {Array.from({ length: count }).map((_, index) => (
                <div key={index} className="card p-5">
                    <div className="flex gap-4">
                        <div className="skeleton h-8 w-8 rounded-full" />
                        <div className="flex-1 space-y-3">
                            <div className="skeleton h-4 w-2/5" />
                            <div className="skeleton h-3 w-4/5" />
                            <div className="flex gap-2">
                                <div className="skeleton h-6 w-20 rounded-full" />
                                <div className="skeleton h-6 w-24 rounded-full" />
                            </div>
                        </div>
                    </div>
                </div>
            ))}
        </div>
    );
}

export function DashboardSkeleton() {
    return (
        <div className="space-y-4" aria-hidden="true">
            <div className="skeleton h-24 w-full rounded-xl" />
            <div className="grid gap-3 md:grid-cols-3">
                {Array.from({ length: 3 }).map((_, index) => (
                    <div key={index} className="skeleton h-16 rounded-xl" />
                ))}
            </div>
            <div className="skeleton h-20 w-full rounded-xl" />
            <div className="grid gap-4 lg:grid-cols-2">
                <div className="skeleton h-64 rounded-xl" />
                <div className="skeleton h-64 rounded-xl" />
            </div>
        </div>
    );
}
