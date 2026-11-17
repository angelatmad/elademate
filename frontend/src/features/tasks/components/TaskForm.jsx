import { useState } from 'react';
import { Link } from 'react-router-dom';
import { CalendarDays, Clock3, Eye, Save, Sparkles } from 'lucide-react';
import Alert from '@/components/ui/Alert';
import { CategoryBadge, PriorityBadge, StatusBadge } from '@/components/ui/Badge';
import { Spinner } from '@/components/ui/Loading';
import { getApiError } from '@/services/api/client';
import { CATEGORIES, PRIORITIES, formatDate, formatTime } from '@/utils/taskMeta';

const emptyTask = {
    title: '',
    description: '',
    category: '',
    priority: 'medium',
    status: 'pending',
    due_date: '',
    due_time: '',
};

function valuesFromTask(task) {
    if (!task) {
        return emptyTask;
    }

    return {
        title: task.title || '',
        description: task.description || '',
        category: task.category || '',
        priority: task.priority || 'medium',
        status: task.status || 'pending',
        due_date: task.due_date || '',
        due_time: task.due_time || '',
    };
}

export default function TaskForm({ task = null, onSubmit, cancelTo = '/tasks', submitLabel }) {
    const [values, setValues] = useState(() => valuesFromTask(task));
    const [errors, setErrors] = useState({});
    const [generalError, setGeneralError] = useState('');
    const [success, setSuccess] = useState('');
    const [submitting, setSubmitting] = useState(false);

    const setField = (field, value) => {
        setValues((current) => ({ ...current, [field]: value }));
        setErrors((current) => ({ ...current, [field]: undefined }));
        setGeneralError('');
        setSuccess('');
    };

    const validate = () => {
        const nextErrors = {};

        if (!values.title.trim()) {
            nextErrors.title = 'Please enter a task title.';
        }

        if (!values.category) {
            nextErrors.category = 'Please select a subject.';
        }

        if (!values.due_date) {
            nextErrors.due_date = 'Please choose a due date.';
        }

        if (!values.due_time) {
            nextErrors.due_time = 'Please choose a due time.';
        }

        if (!values.priority) {
            nextErrors.priority = 'Please select a priority.';
        }

        setErrors(nextErrors);

        return Object.keys(nextErrors).length === 0;
    };

    const handleSubmit = async (event) => {
        event.preventDefault();

        if (!validate()) {
            return;
        }

        setSubmitting(true);
        setGeneralError('');
        setSuccess('');
        setErrors({});

        try {
            await onSubmit({
                title: values.title.trim(),
                description: values.description.trim() || null,
                category: values.category,
                priority: values.priority,
                status: values.status || 'pending',
                due_date: values.due_date,
                due_time: values.due_time,
            });
            setSuccess('Your task was saved successfully.');
        } catch (error) {
            const apiError = getApiError(error, 'We could not save this task. Please try again.');
            setErrors(apiError.errors);
            setGeneralError(apiError.status === 422 ? 'Please check the highlighted fields.' : apiError.message);
        } finally {
            setSubmitting(false);
        }
    };

    const subjectOptions = values.category && !CATEGORIES.includes(values.category)
        ? [values.category, ...CATEGORIES]
        : CATEGORIES;

    return (
        <form onSubmit={handleSubmit} className="space-y-5" noValidate>
            {generalError && <Alert message={generalError} onClose={() => setGeneralError('')} />}
            {success && <Alert message={success} type="success" onClose={() => setSuccess('')} />}

            <div className="grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px] 2xl:grid-cols-[minmax(0,1fr)_400px]">
                <div className="min-w-0 space-y-5">
                    <section className="card p-5 sm:p-6" aria-labelledby="form-heading">
                        <div className="flex items-center gap-2.5">
                            <span className="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                <Sparkles className="h-4 w-4" />
                            </span>
                            <div>
                                <h2 id="form-heading" className="text-base font-semibold text-ink-900">Task details</h2>
                                <p className="text-xs text-ink-500">Fill in the details below to add your task.</p>
                            </div>
                        </div>

                        <div className="mt-5 space-y-4">
                            <div>
                                <label htmlFor="title" className="label">
                                    Task Title <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    id="title"
                                    name="title"
                                    type="text"
                                    value={values.title}
                                    onChange={(event) => setField('title', event.target.value)}
                                    placeholder="Enter task title"
                                    className="input bg-[#FDF2F6] focus:bg-white"
                                    maxLength={120}
                                    autoFocus
                                    required
                                    aria-invalid={Boolean(errors.title)}
                                    aria-describedby={errors.title ? 'title-error' : undefined}
                                />
                                {errors.title && <p id="title-error" className="error-text">{errors.title}</p>}
                            </div>

                            <div>
                                <label htmlFor="category" className="label">
                                    Subject <span className="text-rose-500">*</span>
                                </label>
                                <select
                                    id="category"
                                    name="category"
                                    value={values.category}
                                    onChange={(event) => setField('category', event.target.value)}
                                    className="input appearance-none bg-[#FDF2F6] focus:bg-white"
                                    required
                                    aria-invalid={Boolean(errors.category)}
                                    aria-describedby={errors.category ? 'category-error' : undefined}
                                >
                                    <option value="">Select a subject</option>
                                    {subjectOptions.map((category) => (
                                        <option key={category} value={category}>{category}</option>
                                    ))}
                                </select>
                                {errors.category && <p id="category-error" className="error-text">{errors.category}</p>}
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label htmlFor="due_date" className="label">
                                        Due Date <span className="text-rose-500">*</span>
                                    </label>
                                    <input
                                        id="due_date"
                                        name="due_date"
                                        type="date"
                                        value={values.due_date}
                                        onChange={(event) => setField('due_date', event.target.value)}
                                        className="input bg-[#FDF2F6] focus:bg-white"
                                        required
                                        aria-invalid={Boolean(errors.due_date)}
                                        aria-describedby={errors.due_date ? 'due_date-error' : undefined}
                                    />
                                    {errors.due_date && <p id="due_date-error" className="error-text">{errors.due_date}</p>}
                                </div>

                                <div>
                                    <label htmlFor="due_time" className="label">
                                        Due Time <span className="text-rose-500">*</span>
                                    </label>
                                    <input
                                        id="due_time"
                                        name="due_time"
                                        type="time"
                                        value={values.due_time}
                                        onChange={(event) => setField('due_time', event.target.value)}
                                        className="input bg-[#FDF2F6] focus:bg-white"
                                        required
                                        aria-invalid={Boolean(errors.due_time)}
                                        aria-describedby={errors.due_time ? 'due_time-error' : undefined}
                                    />
                                    {errors.due_time && <p id="due_time-error" className="error-text">{errors.due_time}</p>}
                                </div>
                            </div>

                            <div>
                                <label htmlFor="priority" className="label">
                                    Priority <span className="text-rose-500">*</span>
                                </label>
                                <select
                                    id="priority"
                                    name="priority"
                                    value={values.priority}
                                    onChange={(event) => setField('priority', event.target.value)}
                                    className="input appearance-none bg-[#FDF2F6] focus:bg-white"
                                    required
                                    aria-invalid={Boolean(errors.priority)}
                                    aria-describedby={errors.priority ? 'priority-error' : undefined}
                                >
                                    {PRIORITIES.map((priority) => (
                                        <option key={priority.value} value={priority.value}>{priority.label}</option>
                                    ))}
                                </select>
                                {errors.priority && <p id="priority-error" className="error-text">{errors.priority}</p>}
                            </div>

                            <div>
                                <label htmlFor="description" className="label">
                                    Description <span className="font-normal text-ink-500">(Optional)</span>
                                </label>
                                <textarea
                                    id="description"
                                    name="description"
                                    value={values.description}
                                    onChange={(event) => setField('description', event.target.value)}
                                    placeholder="Add additional details about this task..."
                                    className="input min-h-32 resize-y bg-[#FDF2F6] focus:bg-white"
                                    rows={4}
                                    maxLength={2000}
                                />
                                {errors.description && <p className="error-text">{errors.description}</p>}
                            </div>
                        </div>

                        <div className="mt-6 flex items-center justify-center gap-3 border-t border-brand-100 pt-5">
                            <Link
                                to={cancelTo}
                                className="btn-secondary min-w-[120px] justify-center border-[#F52D70] text-[#D4145A] hover:border-[#D4145A] hover:bg-[#FDF2F6] hover:text-[#B80F4A] sm:min-w-[150px]"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                className="btn-primary min-w-[120px] justify-center sm:min-w-[150px]"
                                disabled={submitting}
                            >
                                {submitting ? <Spinner className="h-4 w-4" /> : <Save className="h-4 w-4" />}
                                {submitting ? 'Saving...' : (submitLabel || 'Save Task')}
                            </button>
                        </div>
                    </section>
                </div>

                <div className="space-y-5 lg:sticky lg:top-[108px] lg:self-start">
                    <section className="card overflow-hidden" aria-labelledby="preview-heading">
                        <div className="flex items-center justify-between border-b border-brand-50 px-5 py-3.5">
                            <div className="flex items-center gap-2">
                                <Eye className="h-4 w-4 text-brand-600" />
                                <h2 id="preview-heading" className="text-sm font-semibold text-ink-900">Task preview</h2>
                            </div>
                            <span className="text-[10px] font-semibold uppercase tracking-[0.14em] text-ink-500">Live</span>
                        </div>
                        <div className="bg-gradient-to-br from-brand-50/70 to-white p-5">
                            <div className="flex flex-wrap gap-2">
                                <PriorityBadge value={values.priority} />
                                <StatusBadge value={values.status} />
                            </div>
                            <h3 className={`mt-3 line-clamp-2 text-lg font-semibold ${values.title ? 'text-ink-900' : 'text-ink-500/60'}`}>
                                {values.title || 'Your task title will appear here'}
                            </h3>
                            <p className={`mt-2 line-clamp-3 text-xs leading-relaxed ${values.description ? 'text-ink-500' : 'text-ink-500/60'}`}>
                                {values.description || 'Add a short description so future you knows exactly where to start.'}
                            </p>
                            <div className="mt-4 flex flex-wrap items-center gap-2">
                                <CategoryBadge category={values.category} />
                                <span className="inline-flex items-center gap-1.5 rounded-full bg-white px-2.5 py-1 text-[11px] font-medium text-ink-700 ring-1 ring-brand-100">
                                    <CalendarDays className="h-3.5 w-3.5 text-brand-500" />
                                    {values.due_date ? formatDate(values.due_date) : 'No due date'}
                                </span>
                                {values.due_time && (
                                    <span className="inline-flex items-center gap-1.5 rounded-full bg-white px-2.5 py-1 text-[11px] font-medium text-ink-700 ring-1 ring-brand-100">
                                        <Clock3 className="h-3.5 w-3.5 text-brand-500" />
                                        {formatTime(values.due_time)}
                                    </span>
                                )}
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </form>
    );
}
