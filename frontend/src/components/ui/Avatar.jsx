import { useEffect, useState } from 'react';

const sizes = {
    sm: 'h-8 w-8 text-[10px]',
    md: 'h-9 w-9 text-xs',
    lg: 'h-12 w-12 text-sm sm:h-14 sm:w-14 sm:text-base',
    xl: 'h-16 w-16 text-xl sm:h-24 sm:w-24 sm:text-2xl',
};

export default function Avatar({ user, size = 'md', className = '' }) {
    const [imgError, setImgError] = useState(false);
    const dimension = sizes[size] ?? sizes.md;
    const label = user?.name || 'Student';
    const src = user?.avatar_url;

    useEffect(() => {
        setImgError(false);
    }, [src]);

    const showImage = Boolean(src) && !imgError;

    return (
        <span
            className={`inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-brand-400 to-brand-700 font-semibold uppercase text-white ring-2 ring-white ${dimension} ${className}`}
            aria-label={label}
            title={label}
        >
            {showImage ? (
                <img
                    src={src}
                    alt={label}
                    className="h-full w-full object-cover"
                    onError={() => setImgError(true)}
                    loading="lazy"
                    referrerPolicy="no-referrer"
                />
            ) : (
                <span aria-hidden="true">{user?.initials || (label.trim()[0] || 'S')}</span>
            )}
        </span>
    );
}