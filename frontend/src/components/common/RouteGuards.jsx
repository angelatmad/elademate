import { Navigate, Outlet, useLocation } from 'react-router-dom';
import { useAuth } from '@/features/auth/hooks/useAuth';
import { LoadingScreen } from '@/components/ui/Loading';

export function ProtectedRoute() {
    const { token, loading } = useAuth();
    const location = useLocation();

    if (loading) {
        return <LoadingScreen />;
    }

    if (!token) {
        return <Navigate to="/login" replace state={{ from: location }} />;
    }

    return <Outlet />;
}

export function GuestRoute() {
    const { token, loading } = useAuth();

    if (loading) {
        return <LoadingScreen label="Checking your session..." />;
    }

    if (token) {
        return <Navigate to="/dashboard" replace />;
    }

    return <Outlet />;
}
