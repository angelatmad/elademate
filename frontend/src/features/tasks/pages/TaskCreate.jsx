import { useNavigate, useSearchParams } from 'react-router-dom';
import PageHeader from '@/components/ui/PageHeader';
import TaskForm from '@/features/tasks/components/TaskForm';
import api from '@/services/api/client';

export default function TaskCreate() {
    const navigate = useNavigate();
    const [searchParams] = useSearchParams();
    const initialDate = searchParams.get('date') || '';
    const draft = initialDate
        ? { due_date: initialDate, priority: 'medium', status: 'pending' }
        : null;

    const handleSubmit = async (payload) => {
        const response = await api.post('/tasks', payload);
        navigate(`/tasks/${response.data.data.id}`, {
            replace: true,
            state: { message: response.data.message },
        });
    };

    return (
        <div className="space-y-5">
            <PageHeader
                eyebrow="New task"
                title="Add New Task"
                subtitle="Fill in the details below to add a new task to your list."
                back
                backTo="/tasks"
            />
            <TaskForm task={draft} onSubmit={handleSubmit} />
        </div>
    );
}
