import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { ArrowLeft } from 'lucide-react';
import Alert from '@/components/ui/Alert';
import PageHeader from '@/components/ui/PageHeader';
import TaskForm from '@/features/tasks/components/TaskForm';
import { CardSkeleton } from '@/components/ui/Loading';
import api, { getApiError } from '@/services/api/client';

export default function TaskEdit() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [task, setTask] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    useEffect(() => {
        let active = true;

        api.get(`/tasks/${id}`)
            .then((response) => {
                if (active) {
                    setTask(response.data.data);
                }
            })
            .catch((requestError) => {
                if (active) {
                    setError(getApiError(requestError, 'We could not load this task.').message);
                }
            })
            .finally(() => {
                if (active) {
                    setLoading(false);
                }
            });

        return () => {
            active = false;
        };
    }, [id]);

    const handleSubmit = async (payload) => {
        const response = await api.put(`/tasks/${id}`, payload);
        navigate(`/tasks/${id}`, {
            replace: true,
            state: { message: response.data.message },
        });
    };

    if (loading) {
        return (
            <div className="space-y-5">
                <div className="skeleton h-16 w-64" />
                <CardSkeleton count={2} />
            </div>
        );
    }

    if (error || !task) {
        return (
            <div className="space-y-4">
                <Alert message={error || 'Task not found.'} />
                <button type="button" onClick={() => navigate('/tasks')} className="btn-secondary">
                    <ArrowLeft className="h-4 w-4" />
                    Back to tasks
                </button>
            </div>
        );
    }

    return (
        <div className="space-y-5">
            <PageHeader
                eyebrow="Edit task"
                title={task.title}
                subtitle="Adjust the details, schedule, or status whenever life changes."
                back
                backTo={`/tasks/${task.id}`}
            />
            <TaskForm
                task={task}
                onSubmit={handleSubmit}
                cancelTo={`/tasks/${task.id}`}
                submitLabel="Save Changes"
            />
        </div>
    );
}
