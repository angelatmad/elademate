import { Navigate, Route, Routes } from 'react-router-dom';
import AppLayout from '@/components/layout/AppLayout';
import AuthLayout from '@/components/layout/AuthLayout';
import { GuestRoute, ProtectedRoute } from '@/components/common/RouteGuards';
import Login from '@/features/auth/pages/Login';
import Register from '@/features/auth/pages/Register';
import Dashboard from '@/features/dashboard/pages/Dashboard';
import Tasks from '@/features/tasks/pages/Tasks';
import TaskCreate from '@/features/tasks/pages/TaskCreate';
import TaskEdit from '@/features/tasks/pages/TaskEdit';
import TaskShow from '@/features/tasks/pages/TaskShow';
import CalendarPage from '@/features/calendar/pages/CalendarPage';
import Settings from '@/features/settings/pages/Settings';
import NotFound from '@/features/auth/pages/NotFound';

export default function AppRoutes() {
    return (
        <Routes>
            <Route element={<GuestRoute />}>
                <Route element={<AuthLayout />}>
                    <Route path="/login" element={<Login />} />
                    <Route path="/register" element={<Register />} />
                </Route>
            </Route>

            <Route element={<ProtectedRoute />}>
                <Route element={<AppLayout />}>
                    <Route path="/" element={<Navigate to="/dashboard" replace />} />
                    <Route path="/dashboard" element={<Dashboard />} />
                    <Route path="/tasks" element={<Tasks />} />
                    <Route path="/tasks/new" element={<TaskCreate />} />
                    <Route path="/tasks/:id" element={<TaskShow />} />
                    <Route path="/tasks/:id/edit" element={<TaskEdit />} />
                    <Route path="/calendar" element={<CalendarPage />} />
                    <Route path="/settings" element={<Settings />} />
                </Route>
            </Route>

            <Route path="*" element={<NotFound />} />
        </Routes>
    );
}
