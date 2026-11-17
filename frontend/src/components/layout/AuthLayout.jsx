import { Outlet } from 'react-router-dom';
import logo from '@/assets/logo.png';

export default function AuthLayout() {
    return (
        <div className="relative flex min-h-screen flex-col overflow-x-hidden bg-white">
            <div className="relative bg-[#FCE4EC] pb-32 pt-10 sm:pb-44 sm:pt-16">
                <div className="pointer-events-none absolute left-1/2 top-[45%] h-72 w-72 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#F8D5E2]/45 blur-2xl sm:h-[420px] sm:w-[420px]" aria-hidden="true" />
                <div className="relative z-10 flex justify-center px-4">
                    <img src={logo} alt="EladeMate logo" className="h-32 w-auto max-w-full object-contain sm:h-56" />
                </div>
            </div>

            <main className="relative z-10 mx-auto -mt-20 flex w-full max-w-[420px] flex-1 flex-col px-4 sm:-mt-24 sm:px-0">
                <div className="rounded-3xl border border-[#F8D5E2] bg-white px-5 py-7 shadow-[0_20px_50px_-20px_rgba(165,0,61,0.25)] sm:px-8 sm:py-8">
                    <Outlet />
                </div>

                <footer className="relative z-10 pb-28 pt-8 text-center sm:pb-36 sm:pt-10">
                    <p className="text-xl font-bold tracking-tight text-[#A5003D]">EladeMate</p>
                    <p className="mt-0.5 font-script text-lg text-[#E91E63]">Plan. Track. Succeed.</p>
                </footer>
            </main>

            <div className="pointer-events-none absolute inset-x-0 bottom-0 z-0">
                <svg className="h-28 w-full sm:h-40" viewBox="0 0 1440 220" preserveAspectRatio="none" aria-hidden="true">
                    <defs>
                        <linearGradient id="waveGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stopColor="#A5003D" />
                            <stop offset="100%" stopColor="#E91E63" />
                        </linearGradient>
                    </defs>
                    <path fill="url(#waveGrad)" fillOpacity="0.25" d="M0 120C180 60 360 180 540 150C720 120 900 40 1080 70C1260 100 1360 150 1440 120V220H0V120Z" />
                    <path fill="url(#waveGrad)" d="M0 165C180 115 360 205 540 180C720 155 900 95 1080 120C1260 145 1360 185 1440 165V220H0V165Z" />
                </svg>
            </div>
        </div>
    );
}
