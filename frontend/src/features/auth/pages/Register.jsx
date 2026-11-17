import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Eye, EyeOff, LockKeyhole, Mail, UserRound, UserRoundPlus } from 'lucide-react';
import Alert from '@/components/ui/Alert';
import { Spinner } from '@/components/ui/Loading';
import { useAuth } from '@/features/auth/hooks/useAuth';
import { getApiError } from '@/services/api/client';

export default function Register() {
    const { register } = useAuth();
    const navigate = useNavigate();

    const [values, setValues] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });
    const [showPassword, setShowPassword] = useState(false);
    const [errors, setErrors] = useState({});
    const [error, setError] = useState('');
    const [submitting, setSubmitting] = useState(false);

    const setField = (field, value) => {
        setValues((current) => ({ ...current, [field]: value }));
        setErrors((current) => ({ ...current, [field]: undefined }));
        setError('');
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setSubmitting(true);
        setError('');
        setErrors({});

        try {
            await register({ ...values, device_name: 'elademate-react' });
            navigate('/dashboard', { replace: true });
        } catch (requestError) {
            const apiError = getApiError(requestError, 'Unable to create your account. Please try again.');
            setErrors(apiError.errors);
            setError(apiError.status === 422 ? 'Please check the highlighted fields.' : apiError.message);
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <div className="animate-float-in">
            <p className="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Get started</p>
            <h1 className="mt-2 text-3xl font-bold tracking-tight text-ink-900">Create your account</h1>
            <p className="mt-2 text-sm leading-relaxed text-ink-500">
                A calmer, clearer way to handle everything on your student plate.
            </p>

            <div className="mt-6">
                <Alert message={error} onClose={() => setError('')} />
            </div>

            <form onSubmit={handleSubmit} className="mt-5 space-y-4" noValidate>
                <div>
                    <label htmlFor="name" className="label">Full name</label>
                    <div className="relative">
                        <UserRound className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-500" />
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value={values.name}
                            onChange={(event) => setField('name', event.target.value)}
                            className="input pl-10"
                            placeholder="Alyssa Cruz"
                            autoComplete="name"
                            autoFocus
                            required
                            maxLength={100}
                            aria-invalid={Boolean(errors.name)}
                        />
                    </div>
                    {errors.name && <p className="error-text">{errors.name[0]}</p>}
                </div>

                <div>
                    <label htmlFor="email" className="label">Email address</label>
                    <div className="relative">
                        <Mail className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-500" />
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value={values.email}
                            onChange={(event) => setField('email', event.target.value)}
                            className="input pl-10"
                            placeholder="you@school.edu"
                            autoComplete="email"
                            required
                            aria-invalid={Boolean(errors.email)}
                        />
                    </div>
                    {errors.email && <p className="error-text">{errors.email[0]}</p>}
                </div>

                <div>
                    <label htmlFor="password" className="label">Password</label>
                    <div className="relative">
                        <LockKeyhole className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-500" />
                        <input
                            id="password"
                            name="password"
                            type={showPassword ? 'text' : 'password'}
                            value={values.password}
                            onChange={(event) => setField('password', event.target.value)}
                            className="input pl-10 pr-11"
                            placeholder="At least 8 characters"
                            autoComplete="new-password"
                            required
                            aria-invalid={Boolean(errors.password)}
                        />
                        <button
                            type="button"
                            onClick={() => setShowPassword((value) => !value)}
                            className="absolute right-2.5 top-1/2 -translate-y-1/2 rounded-lg p-1.5 text-ink-500 transition hover:bg-brand-50 hover:text-brand-700"
                            aria-label={showPassword ? 'Hide password' : 'Show password'}
                        >
                            {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                        </button>
                    </div>
                    {errors.password && <p className="error-text">{errors.password[0]}</p>}
                </div>

                <div>
                    <label htmlFor="password_confirmation" className="label">Confirm password</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type={showPassword ? 'text' : 'password'}
                        value={values.password_confirmation}
                        onChange={(event) => setField('password_confirmation', event.target.value)}
                        className="input"
                        placeholder="Repeat your password"
                        autoComplete="new-password"
                        required
                    />
                </div>

                <button type="submit" className="btn-primary w-full" disabled={submitting}>
                    {submitting ? <Spinner className="h-4 w-4" /> : <UserRoundPlus className="h-4 w-4" />}
                    {submitting ? 'Creating account...' : 'Create account'}
                </button>
            </form>

            <p className="mt-6 text-center text-sm text-ink-500">
                Already have an account?{' '}
                <Link to="/login" className="font-semibold text-brand-700 hover:text-brand-800">
                    Sign in
                </Link>
            </p>
        </div>
    );
}
