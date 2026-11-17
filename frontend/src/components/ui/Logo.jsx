import { Link } from 'react-router-dom';
import logo from '@/assets/logo.png';

export default function Logo({ to = '/dashboard', compact = false, showText = false, size, className = '' }) {
    const height = size ?? (compact ? 'h-8' : 'h-10');

    return (
        <Link
            to={to}
            className={`inline-flex flex-col items-center justify-center gap-1 ${className}`}
            aria-label="EladeMate home"
        >
            <img
                src={logo}
                alt="EladeMate logo"
                className={`w-auto object-contain ${height}`}
            />
            {showText && <span className="text-xs font-semibold text-[#D4145A]">EladeMate</span>}
        </Link>
    );
}
