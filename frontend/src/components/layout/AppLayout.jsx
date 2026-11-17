import { useEffect, useState } from 'react';
import { Outlet, useLocation } from 'react-router-dom';
import Sidebar from '@/components/layout/Sidebar';
import Topbar from '@/components/layout/Topbar';

export default function AppLayout() {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const location = useLocation();

    useEffect(() => {
        setSidebarOpen(false);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }, [location.pathname]);

    return (
        <div className="min-h-screen overflow-x-hidden bg-white">
            <Sidebar open={sidebarOpen} onClose={() => setSidebarOpen(false)} />

            <div className="flex min-h-screen flex-col md:pl-[220px]">
                <Topbar onOpenSidebar={() => setSidebarOpen(true)} />

                <main id="main-content" className="min-w-0 flex-1 overflow-x-hidden bg-white px-3 py-3 sm:px-6 sm:py-5 lg:px-8">
                    <div className="mx-auto w-full max-w-[1180px]">
                        <Outlet />
                    </div>
                </main>
            </div>
        </div>
    );
}
