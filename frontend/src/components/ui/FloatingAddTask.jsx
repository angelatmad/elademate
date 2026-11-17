import { Link } from 'react-router-dom';
import { Plus } from 'lucide-react';

export default function FloatingAddTask({ to = '/tasks/new', label = 'Add Task' }) {
    return (
        <div className="group fixed bottom-[calc(1.5rem+env(safe-area-inset-bottom))] right-[calc(1.5rem+env(safe-area-inset-right))] z-30 print:hidden">
            <Link
                to={to}
                aria-label={label}
                className="relative flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-[#F52D70] to-[#D4145A] text-white shadow-lg shadow-[#D4145A]/30 transition duration-200 hover:from-[#D4145A] hover:to-[#B80F4A] hover:shadow-xl hover:scale-105 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#D4145A]/40 focus-visible:ring-offset-2"
            >
                <Plus className="h-6 w-6" strokeWidth={2.5} aria-hidden="true" />
            </Link>
            <span
                role="tooltip"
                className="pointer-events-none absolute bottom-full right-0 mb-3 whitespace-nowrap rounded-md bg-gray-900 px-2.5 py-1.5 text-xs font-medium text-white opacity-0 shadow-md transition-opacity duration-200 group-hover:opacity-100 group-focus-within:opacity-100"
            >
                {label}
                <span className="absolute right-6 top-full border-4 border-transparent border-t-gray-900" aria-hidden="true" />
            </span>
        </div>
    );
}
