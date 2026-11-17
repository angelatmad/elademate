import { useEffect, useRef, useState } from 'react';
import { Camera, CheckCircle2, KeyRound, Save, Trash2, UserRound } from 'lucide-react';
import Alert from '@/components/ui/Alert';
import Avatar from '@/components/ui/Avatar';
import PageHeader from '@/components/ui/PageHeader';
import { Spinner } from '@/components/ui/Loading';
import { useAuth } from '@/features/auth/hooks/useAuth';
import api, { getApiError } from '@/services/api/client';

export default function Settings() {
    const { user, updateProfile, changePassword } = useAuth();
    const fileInputRef = useRef(null);

    const [profile, setProfile] = useState({ name: '', email: '' });
    const [photo, setPhoto] = useState(null);
    const [photoPreview, setPhotoPreview] = useState(null);
    const [removePhoto, setRemovePhoto] = useState(false);
    const [stats, setStats] = useState({ total: 0, completed: 0, pending: 0 });
    const [profileErrors, setProfileErrors] = useState({});
    const [profileMessage, setProfileMessage] = useState('');
    const [profileError, setProfileError] = useState('');
    const [savingProfile, setSavingProfile] = useState(false);

    const [password, setPassword] = useState({
        current_password: '',
        password: '',
        password_confirmation: '',
    });
    const [passwordErrors, setPasswordErrors] = useState({});
    const [passwordMessage, setPasswordMessage] = useState('');
    const [passwordError, setPasswordError] = useState('');
    const [savingPassword, setSavingPassword] = useState(false);

    useEffect(() => {
        if (user) {
            setProfile({ name: user.name || '', email: user.email || '' });
        }
    }, [user]);

    useEffect(() => {
        let active = true;

        api.get('/profile')
            .then((response) => {
                if (active) {
                    setStats(response.data.data.stats);
                }
            })
            .catch(() => {});

        return () => {
            active = false;
        };
    }, [profileMessage]);

    useEffect(() => () => {
        if (photoPreview) {
            URL.revokeObjectURL(photoPreview);
        }
    }, [photoPreview]);

    const handlePhotoChange = (event) => {
        const file = event.target.files?.[0] || null;
        setPhoto(file);
        setRemovePhoto(false);
        setProfileErrors((current) => ({ ...current, profile_photo: undefined }));

        if (photoPreview) {
            URL.revokeObjectURL(photoPreview);
        }

        setPhotoPreview(file ? URL.createObjectURL(file) : null);
    };

    const handleRemovePhoto = () => {
        setPhoto(null);
        setRemovePhoto(true);
        setProfileErrors((current) => ({ ...current, profile_photo: undefined }));

        if (photoPreview) {
            URL.revokeObjectURL(photoPreview);
        }

        setPhotoPreview(null);

        if (fileInputRef.current) {
            fileInputRef.current.value = '';
        }
    };

    const handleProfileSubmit = async (event) => {
        event.preventDefault();
        setSavingProfile(true);
        setProfileMessage('');
        setProfileError('');
        setProfileErrors({});

        const formData = new FormData();
        formData.append('name', profile.name);
        formData.append('email', profile.email);

        if (photo) {
            formData.append('profile_photo', photo);
        }

        if (removePhoto) {
            formData.append('remove_photo', '1');
        }

        try {
            const response = await updateProfile(formData);
            setProfileMessage(response.message);
            setPhoto(null);
            setRemovePhoto(false);
            setPhotoPreview(null);

            if (fileInputRef.current) {
                fileInputRef.current.value = '';
            }
        } catch (requestError) {
            const apiError = getApiError(requestError, 'We could not update your profile.');
            setProfileErrors(apiError.errors);
            setProfileError(apiError.status === 422 ? 'Please check the highlighted fields.' : apiError.message);
        } finally {
            setSavingProfile(false);
        }
    };

    const handlePasswordSubmit = async (event) => {
        event.preventDefault();
        setSavingPassword(true);
        setPasswordMessage('');
        setPasswordError('');
        setPasswordErrors({});

        try {
            const response = await changePassword(password);
            setPasswordMessage(response.message);
            setPassword({ current_password: '', password: '', password_confirmation: '' });
        } catch (requestError) {
            const apiError = getApiError(requestError, 'We could not change your password.');
            setPasswordErrors(apiError.errors);
            setPasswordError(apiError.status === 422 ? 'Please check the highlighted fields.' : apiError.message);
        } finally {
            setSavingPassword(false);
        }
    };

    const previewUser = {
        ...user,
        name: profile.name || user?.name,
        avatar_url: photoPreview || (removePhoto ? null : user?.avatar_url),
    };

    return (
        <div className="mx-auto w-full max-w-[1400px] space-y-5">
            <PageHeader
                eyebrow="Your account"
                title="Settings"
                subtitle="Update your profile, keep your account secure, and see your task totals."
            />

            <div className="grid gap-4 md:grid-cols-3">
                <div className="card p-4">
                    <p className="text-[10px] font-semibold uppercase tracking-[0.16em] text-ink-500">Total tasks</p>
                    <p className="mt-1 text-2xl font-bold text-ink-900">{stats.total}</p>
                </div>
                <div className="card p-4">
                    <p className="text-[10px] font-semibold uppercase tracking-[0.16em] text-ink-500">Completed</p>
                    <p className="mt-1 text-2xl font-bold text-emerald-600">{stats.completed}</p>
                </div>
                <div className="card p-4">
                    <p className="text-[10px] font-semibold uppercase tracking-[0.16em] text-ink-500">Still active</p>
                    <p className="mt-1 text-2xl font-bold text-brand-600">{stats.pending}</p>
                </div>
            </div>

            <div className="grid gap-5 xl:grid-cols-[1.25fr_1fr]">
                <section className="card p-5 sm:p-6">
                    <div className="flex items-center gap-2.5">
                        <span className="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                            <UserRound className="h-4 w-4" />
                        </span>
                        <div>
                            <h2 className="section-title">Profile information</h2>
                            <p className="text-xs text-ink-500">This is the name and email shown across EladeMate.</p>
                        </div>
                    </div>

                    <div className="mt-5">
                        <Alert message={profileMessage} type="success" onClose={() => setProfileMessage('')} />
                        <Alert message={profileError} onClose={() => setProfileError('')} className={profileMessage ? 'mt-3' : ''} />
                    </div>

                    <form onSubmit={handleProfileSubmit} className="mt-5 space-y-4" encType="multipart/form-data">
                        <div className="flex flex-col gap-4 rounded-2xl border border-brand-100 bg-brand-50/40 p-4 sm:flex-row sm:items-center">
                            <Avatar user={previewUser} size="lg" />
                            <div className="min-w-0 flex-1">
                                <p className="text-sm font-semibold text-ink-900">Profile photo</p>
                                <p className="mt-0.5 text-[11px] text-ink-500">JPG, PNG, or WEBP. Maximum 2 MB.</p>
                                <div className="mt-2 flex flex-wrap gap-2">
                                    <button type="button" onClick={() => fileInputRef.current?.click()} className="btn-secondary !py-2 text-xs">
                                        <Camera className="h-3.5 w-3.5" />
                                        Change photo
                                    </button>
                                    {user?.avatar_url && !removePhoto && (
                                        <button type="button" onClick={handleRemovePhoto} className="btn-ghost !py-2 text-xs text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                                            <Trash2 className="h-3.5 w-3.5" />
                                            Remove
                                        </button>
                                    )}
                                </div>
                                {photo && <p className="mt-2 truncate text-[11px] text-brand-700">Selected: {photo.name}</p>}
                                <input
                                    ref={fileInputRef}
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    onChange={handlePhotoChange}
                                    className="hidden"
                                />
                                {profileErrors.profile_photo && <p className="error-text">{profileErrors.profile_photo[0]}</p>}
                            </div>
                        </div>

                        <div>
                            <label htmlFor="name" className="label">Full name</label>
                            <input
                                id="name"
                                type="text"
                                value={profile.name}
                                onChange={(event) => setProfile((current) => ({ ...current, name: event.target.value }))}
                                className="input"
                                maxLength={100}
                                required
                                aria-invalid={Boolean(profileErrors.name)}
                            />
                            {profileErrors.name && <p className="error-text">{profileErrors.name[0]}</p>}
                        </div>

                        <div>
                            <label htmlFor="email" className="label">Email address</label>
                            <input
                                id="email"
                                type="email"
                                value={profile.email}
                                onChange={(event) => setProfile((current) => ({ ...current, email: event.target.value }))}
                                className="input"
                                autoComplete="email"
                                required
                                aria-invalid={Boolean(profileErrors.email)}
                            />
                            {profileErrors.email && <p className="error-text">{profileErrors.email[0]}</p>}
                        </div>

                        <div className="flex justify-end">
                            <button type="submit" className="btn-primary" disabled={savingProfile}>
                                {savingProfile ? <Spinner className="h-4 w-4" /> : <Save className="h-4 w-4" />}
                                {savingProfile ? 'Saving...' : 'Save profile'}
                            </button>
                        </div>
                    </form>
                </section>

                <section className="card p-5 sm:p-6">
                    <div className="flex items-center gap-2.5">
                        <span className="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <KeyRound className="h-4 w-4" />
                        </span>
                        <div>
                            <h2 className="section-title">Change password</h2>
                            <p className="text-xs text-ink-500">Use at least 8 characters and keep it unique.</p>
                        </div>
                    </div>

                    <div className="mt-5">
                        <Alert message={passwordMessage} type="success" onClose={() => setPasswordMessage('')} />
                        <Alert message={passwordError} onClose={() => setPasswordError('')} className={passwordMessage ? 'mt-3' : ''} />
                    </div>

                    <form onSubmit={handlePasswordSubmit} className="mt-5 space-y-4">
                        <div>
                            <label htmlFor="current_password" className="label">Current password</label>
                            <input
                                id="current_password"
                                type="password"
                                value={password.current_password}
                                onChange={(event) => setPassword((current) => ({ ...current, current_password: event.target.value }))}
                                className="input"
                                autoComplete="current-password"
                                required
                                aria-invalid={Boolean(passwordErrors.current_password)}
                            />
                            {passwordErrors.current_password && <p className="error-text">{passwordErrors.current_password[0]}</p>}
                        </div>

                        <div>
                            <label htmlFor="new_password" className="label">New password</label>
                            <input
                                id="new_password"
                                type="password"
                                value={password.password}
                                onChange={(event) => setPassword((current) => ({ ...current, password: event.target.value }))}
                                className="input"
                                autoComplete="new-password"
                                required
                                aria-invalid={Boolean(passwordErrors.password)}
                            />
                            {passwordErrors.password && <p className="error-text">{passwordErrors.password[0]}</p>}
                        </div>

                        <div>
                            <label htmlFor="password_confirmation" className="label">Confirm new password</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                value={password.password_confirmation}
                                onChange={(event) => setPassword((current) => ({ ...current, password_confirmation: event.target.value }))}
                                className="input"
                                autoComplete="new-password"
                                required
                            />
                        </div>

                        <div className="rounded-xl border border-emerald-100 bg-emerald-50/60 p-3 text-[11px] leading-relaxed text-emerald-800">
                            <p className="flex items-start gap-2">
                                <CheckCircle2 className="mt-0.5 h-3.5 w-3.5 shrink-0" />
                                Changing your password keeps your existing session active on this device.
                            </p>
                        </div>

                        <button type="submit" className="btn-primary w-full" disabled={savingPassword}>
                            {savingPassword ? <Spinner className="h-4 w-4" /> : <KeyRound className="h-4 w-4" />}
                            {savingPassword ? 'Updating...' : 'Update password'}
                        </button>
                    </form>
                </section>
            </div>
        </div>
    );
}
