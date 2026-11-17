export default function StatCard({ icon: Icon, label, value }) {
    return (
        <div className="flex min-h-[64px] w-full items-center gap-3 rounded-xl border border-[#F8DCE6] bg-[#FDF2F6] px-4 py-3">
            <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-[#D4145A]">
                {Icon && <Icon className="h-4 w-4" aria-hidden="true" />}
            </span>
            <div className="min-w-0 flex-1">
                <p className="truncate text-xs font-medium text-gray-600">{label}</p>
                <p className="text-xl font-bold leading-tight text-gray-900">{value}</p>
            </div>
        </div>
    );
}
