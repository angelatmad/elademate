@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="mx-auto max-w-5xl space-y-5">
    <section>
        <p class="text-xs font-medium text-ink-500">Your account</p>
        <h1 class="page-title mt-1">Settings</h1>
        <p class="mt-1 text-sm text-ink-500">Manage your profile, password, and account details.</p>
    </section>

    <div class="grid gap-5 lg:grid-cols-3">
        <aside class="space-y-5">
            <section class="card overflow-hidden" aria-labelledby="profile-summary-heading">
                <div class="h-24 bg-gradient-to-br from-brand-400 via-brand-500 to-brand-700"></div>
                <div class="px-5 pb-5">
                    <div class="-mt-10 flex justify-center">
                        <x-avatar :user="$user" size="lg" class="ring-4 ring-white" />
                    </div>
                    <div class="mt-3 text-center">
                        <h2 id="profile-summary-heading" class="text-base font-bold text-ink-900">{{ $user->name }}</h2>
                        <p class="mt-0.5 truncate text-xs text-ink-500">{{ $user->email }}</p>
                        <span class="mt-2 inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-semibold text-emerald-700 ring-1 ring-emerald-200 ring-inset">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            Student account
                        </span>
                    </div>

                    <dl class="mt-5 grid grid-cols-3 gap-2 border-t border-brand-50 pt-4 text-center">
                        <div>
                            <dt class="text-[10px] font-medium text-ink-500">Total</dt>
                            <dd class="text-base font-bold text-ink-900">{{ $totalTasks }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-medium text-ink-500">Done</dt>
                            <dd class="text-base font-bold text-emerald-600">{{ $completedTasks }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-medium text-ink-500">Active</dt>
                            <dd class="text-base font-bold text-brand-600">{{ $pendingTasks }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <section class="card p-5" aria-labelledby="account-info-heading">
                <h2 id="account-info-heading" class="section-title">Account information</h2>
                <dl class="mt-4 space-y-3 text-xs">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-ink-500">Member since</dt>
                        <dd class="font-medium text-ink-900">{{ $user->created_at->format('M j, Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-ink-500">Account ID</dt>
                        <dd class="font-mono text-[11px] font-medium text-ink-700">#{{ str_pad((string) $user->id, 5, '0', STR_PAD_LEFT) }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-ink-500">Location</dt>
                        <dd class="font-medium text-ink-900">Student workspace</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 border-t border-brand-50 pt-3">
                        <dt class="text-ink-500">Last updated</dt>
                        <dd class="font-medium text-ink-900">{{ $user->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-2xl border border-brand-100 bg-gradient-to-br from-brand-50 to-white p-5">
                <div class="flex items-center gap-2 text-brand-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 4 6v6c0 4.4 3.4 8 8 9 4.6-1 8-4.6 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/></svg>
                    <p class="text-xs font-semibold">Your data is yours</p>
                </div>
                <p class="mt-2 text-xs leading-relaxed text-ink-500">Your tasks are private to your account. Only you can view, edit, or delete them.</p>
            </section>
        </aside>

        <div class="space-y-5 lg:col-span-2">
            <section class="card p-5 sm:p-6" aria-labelledby="edit-profile-heading" x-data="{ preview: null, removePhoto: false }">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    </span>
                    <div>
                        <h2 id="edit-profile-heading" class="section-title">Edit profile</h2>
                        <p class="text-xs text-ink-500">Update how you appear inside EladeMate.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-5 space-y-4" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col gap-4 rounded-2xl border border-brand-100 bg-brand-50/40 p-4 sm:flex-row sm:items-center">
                        <div class="relative shrink-0" x-show="!removePhoto">
                            <template x-if="preview">
                                <img :src="preview" alt="New profile photo preview" class="h-20 w-20 rounded-full object-cover ring-4 ring-white shadow-sm">
                            </template>
                            <div x-show="!preview">
                                <x-avatar :user="$user" size="lg" class="ring-4 ring-white" />
                            </div>
                        </div>
                        <div x-show="removePhoto" x-cloak class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full border-2 border-dashed border-brand-200 bg-white text-[10px] font-medium text-ink-500">
                            No photo
                        </div>

                        <div class="min-w-0 flex-1">
                            <label for="profile_photo" class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-brand-200 bg-white px-3.5 py-2 text-xs font-semibold text-brand-700 shadow-sm transition hover:bg-brand-50 focus-within:ring-4 focus-within:ring-brand-100">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16V4M7 9l5-5 5 5"/><path d="M4 15v4a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-4"/></svg>
                                Choose photo
                                <input id="profile_photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null; removePhoto = false">
                            </label>
                            <p class="mt-2 text-[11px] text-ink-500">JPG, PNG, or WebP. Up to 2 MB. Square photos look best.</p>
                            @error('profile_photo')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                            @if ($user->profile_photo)
                                <label for="remove_photo" class="mt-2 flex cursor-pointer items-center gap-2 text-[11px] font-medium text-rose-600">
                                    <input id="remove_photo" name="remove_photo" type="checkbox" value="1" x-model="removePhoto" class="h-3.5 w-3.5 rounded border-brand-200 text-rose-600 focus:ring-rose-300">
                                    Remove current photo
                                </label>
                            @endif
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-form.input label="Full name" name="name" :value="$user->name" placeholder="Your full name" required autocomplete="name" />
                        <x-form.input label="Email address" name="email" type="email" :value="$user->email" placeholder="you@example.com" required autocomplete="email" />
                    </div>

                    <div class="flex flex-col-reverse gap-2 border-t border-brand-50 pt-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-[11px] text-ink-500">Your email is used to log in and is never shown publicly.</p>
                        <button type="submit" class="btn-primary">Save changes</button>
                    </div>
                </form>
            </section>

            <section class="card p-5 sm:p-6" aria-labelledby="change-password-heading">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="3"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                    </span>
                    <div>
                        <h2 id="change-password-heading" class="section-title">Change password</h2>
                        <p class="text-xs text-ink-500">Use at least 8 characters and something you don't reuse.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.password') }}" class="mt-5 space-y-4" novalidate>
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="label">Current password</label>
                        <input id="current_password" name="current_password" type="password" required autocomplete="current-password"
                               placeholder="Enter your current password"
                               @error('current_password') aria-invalid="true" aria-describedby="current_password-error" @enderror
                               class="input">
                        @error('current_password')
                            <p id="current_password-error" class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div x-data="{ show: false }">
                            <label for="password" class="label">New password</label>
                            <div class="relative">
                                <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="new-password"
                                       placeholder="At least 8 characters"
                                       @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                                       class="input pr-10">
                                <button type="button" @click="show = !show" :aria-label="show ? 'Hide new password' : 'Show new password'" class="absolute right-1.5 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-ink-500 transition hover:bg-brand-50 hover:text-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-300">
                                    <svg x-show="!show" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg x-show="show" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18"/><path d="M10.6 6.2A10.9 10.9 0 0 1 12 6c6.5 0 10 6 10 6a17.8 17.8 0 0 1-3.1 3.8M6.6 6.7C3.6 8.5 2 12 2 12s3.5 7 10 7c1.6 0 3-.4 4.2-1.1"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                                </button>
                            </div>
                            @error('password')
                                <p id="password-error" class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-form.input label="Confirm new password" name="password_confirmation" type="password" placeholder="Re-enter new password" required autocomplete="new-password" />
                    </div>

                    <div class="flex flex-col-reverse gap-2 border-t border-brand-50 pt-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-[11px] text-ink-500">You'll stay logged in after changing your password.</p>
                        <button type="submit" class="btn-primary">Update password</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
