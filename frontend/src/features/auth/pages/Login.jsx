import { useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { Eye, EyeOff, LockKeyhole, LogIn, UserRound } from 'lucide-react';
import Alert from '@/components/ui/Alert';
import { Spinner } from '@/components/ui/Loading';
import { useAuth } from '@/features/auth/hooks/useAuth';
import { getApiError } from '@/services/api/client';

function GoogleIcon() {
    return (
        <svg viewBox="0 0 48 48" className="h-4 w-4" aria-hidden="true">
            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
        </svg>
    );
}

export default function Login() {
    const { login } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();
    const from = location.state?.from?.pathname || '/dashboard';

    const [values, setValues] = useState({ email: '', password: '' });
    const [showPassword, setShowPassword] = useState(false);
    const [errors, setErrors] = useState({});
    const [error, setError] = useState('');
    const [notice, setNotice] = useState('');
    const [submitting, setSubmitting] = useState(false);

    const setField = (field, value) => {
        setValues((current) => ({ ...current, [field]: value }));
        setErrors((current) => ({ ...current, [field]: undefined }));
        setError('');
        setNotice('');
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setSubmitting(true);
        setError('');
        setNotice('');
        setErrors({});

        try {
            await login({ ...values, device_name: 'elademate-react' });
            navigate(from, { replace: true });
        } catch (requestError) {
            const apiError = getApiError(requestError, 'Unable to log in. Please try again.');
            setErrors(apiError.errors);
            setError(apiError.status === 422 ? apiError.errors.email?.[0] || apiError.message : apiError.message);
        } finally {
            setSubmitting(false);
        }
    };

    const handleGoogle = () => {
        setError('');
        setNotice('Google sign-in is not available yet. Please log in with your EladeMate account.');
    };

    return (
        <div>
            <div className="text-center">
                <h1 className="font-script text-4xl font-semibold leading-none text-[#E91E63] sm:text-5xl">Welcome back!</h1>
                <p className="mt-2 text-sm text-gray-500">Log in to continue your journey.</p>
            </div>

            <div className="mt-4 space-y-3">
                <Alert message={error} onClose={() => setError('')} />
                <Alert type="info" message={notice} onClose={() => setNotice('')} />
            </div>

            <form onSubmit={handleSubmit} className="mt-5 space-y-4" noValidate>
                <div>
                    <label htmlFor="email" className="mb-1.5 block text-xs font-medium text-gray-600">Student ID or username</label>
                    <div className="relative">
                        <UserRound className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#E91E63]/60" />
                        <input
                            id="email"
                            name="email"
                            type="text"
                            value={values.email}
                            onChange={(event) => setField('email', event.target.value)}
                            className={`w-full rounded-xl border bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 ${errors.email ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-[#F8D5E2] focus:border-[#E91E63] focus:ring-[#FCE4EC]'}`}
                            placeholder="Enter your student ID or username"
                            autoComplete="username"
                            inputMode="email"
                            autoFocus
                            required
                            aria-invalid={Boolean(errors.email)}
                        />
                    </div>
                    {errors.email && <p className="mt-1 text-xs text-rose-600">{errors.email[0]}</p>}
                </div>

                <div>
                    <label htmlFor="password" className="mb-1.5 block text-xs font-medium text-gray-600">Password</label>
                    <div className="relative">
                        <LockKeyhole className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#E91E63]/60" />
                        <input
                            id="password"
                            name="password"
                            type={showPassword ? 'text' : 'password'}
                            value={values.password}
                            onChange={(event) => setField('password', event.target.value)}
                            className={`w-full rounded-xl border bg-white py-2.5 pl-10 pr-11 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 ${errors.password ? 'border-rose-300 focus:border-rose-400 focus:ring-rose-100' : 'border-[#F8D5E2] focus:border-[#E91E63] focus:ring-[#FCE4EC]'}`}
                            placeholder="Enter your password"
                            autoComplete="current-password"
                            required
                            aria-invalid={Boolean(errors.password)}
                        />
                        <button
                            type="button"
                            onClick={() => setShowPassword((value) => !value)}
                            className="absolute right-2.5 top-1/2 -translate-y-1/2 rounded-md p-1.5 text-gray-400 transition hover:bg-[#FCE4EC] hover:text-[#E91E63]"
                            aria-label={showPassword ? 'Hide password' : 'Show password'}
                        >
                            {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                        </button>
                    </div>
                    {errors.password && <p className="mt-1 text-xs text-rose-600">{errors.password[0]}</p>}
                </div>

                <button
                    type="submit"
                    className="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#E91E63] to-[#A5003D] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:from-[#d81b60] hover:to-[#8c0034] focus:outline-none focus:ring-2 focus:ring-[#F8D5E2] disabled:cursor-not-allowed disabled:opacity-60"
                    disabled={submitting}
                >
                    {submitting ? <Spinner className="h-4 w-4" /> : <LogIn className="h-4 w-4" />}
                    {submitting ? 'Logging in...' : 'Log In'}
                </button>
            </form>

            <div className="my-5 flex items-center gap-3">
                <span className="h-px flex-1 bg-[#F8D5E2]" />
                <span className="text-[11px] font-medium lowercase text-gray-400">or</span>
                <span className="h-px flex-1 bg-[#F8D5E2]" />
            </div>

            <button
                type="button"
                onClick={handleGoogle}
                className="inline-flex w-full items-center justify-center gap-2.5 rounded-xl border border-[#F8D5E2] bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-[#FCE4EC]/40 focus:outline-none focus:ring-2 focus:ring-[#FCE4EC]"
            >
                <GoogleIcon />
                Continue with Google
            </button>

            <p className="mt-5 text-center text-sm text-gray-500">
                Don&apos;t have an account?{' '}
                <Link to="/register" className="font-semibold text-[#E91E63] transition hover:text-[#A5003D]">
                    Register
                </Link>
            </p>
        </div>
    );
}
