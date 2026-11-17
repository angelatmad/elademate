import BackButton from '@/components/ui/BackButton';

export default function PageHeader({ eyebrow, title, subtitle, actions, back = false, backTo }) {
    const hasActions = Boolean(actions) || back;

    return (
        <section className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div className="min-w-0">
                {eyebrow && <p className="text-xs font-medium text-ink-500">{eyebrow}</p>}
                <h1 className="page-title mt-1">{title}</h1>
                {subtitle && <p className="mt-1 max-w-2xl text-sm text-ink-500">{subtitle}</p>}
            </div>
            {hasActions && (
                <div className="flex shrink-0 flex-wrap items-center justify-end gap-2">
                    {actions}
                    {back && <BackButton to={backTo} />}
                </div>
            )}
        </section>
    );
}
