@extends('layouts.auth')

@section('title', 'Create account')

@section('content')
<div class="min-h-screen lg:grid lg:grid-cols-[1fr_1.05fr]">
    <section class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8">
        <div class="w-full max-w-[440px]">
            <div class="mb-8 flex justify-center lg:hidden">
                <x-logo />
            </div>

            <div class="rounded-3xl border border-brand-100 bg-white p-6 shadow-[0_30px_80px_-40px_rgba(194,24,91,0.45)] sm:p-8">
                <span class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-3 py-1 text-[10px] font-semibold tracking-[0.14em] text-brand-700 uppercase">
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    Join EladeMate
                </span>
                <h1 class="mt-4 text-[26px] font-bold tracking-tight text-ink-900">Create your account</h1>
                <p class="mt-1 text-sm text-ink-500">Start organizing your school life in a calmer way.</p>

                <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4" novalidate>
                    @csrf

                    <x-form.input label="Full name" name="name" :value="old('name')" placeholder="Alyssa Cruz" required autocomplete="name" autofocus />
                    <x-form.input label="Email address" name="email" type="email" :value="old('email')" placeholder="you@example.com" required autocomplete="username" />

                    <div x-data="{ show: false }">
                        <label for="password" class="label">Password <span class="text-brand-500" aria-hidden="true">*</span></label>
                        <div class="relative">
                            <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="new-password"
                                   placeholder="At least 8 characters"
                                   @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                                   class="input pr-12">
                            <button type="button" @click="show = !show" :aria-label="show ? 'Hide password' : 'Show password'" class="absolute right-1.5 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-ink-500 transition hover:bg-brand-50 hover:text-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-300">
                                <svg x-show="!show" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="show" x-cloak class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18"/><path d="M10.6 6.2A10.9 10.9 0 0 1 12 6c6.5 0 10 6 10 6a17.8 17.8 0 0 1-3.1 3.8M6.6 6.7C3.6 8.5 2 12 2 12s3.5 7 10 7c1.6 0 3-.4 4.2-1.1"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p id="password-error" class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-form.input label="Confirm password" name="password_confirmation" type="password" placeholder="Re-enter your password" required autocomplete="new-password" />

                    <button type="submit" class="btn-primary w-full py-3">
                        Create account
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                </form>

                <p class="mt-5 text-center text-sm text-ink-500">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-brand-700 transition hover:text-brand-800">Log in</a>
                </p>
            </div>
        </div>
    </section>

    <section class="relative hidden overflow-hidden bg-gradient-to-br from-brand-800 via-brand-600 to-[#f43f7f] p-10 lg:flex lg:flex-col lg:justify-between xl:p-14" aria-label="About EladeMate">
        <div class="pointer-events-none absolute -right-24 top-20 h-80 w-80 rounded-full bg-white/10 blur-2xl"></div>
        <div class="pointer-events-none absolute -left-20 bottom-0 h-96 w-96 rounded-full bg-brand-300/20 blur-3xl"></div>

        <div class="relative">
            <x-logo light :href="route('register')" />
        </div>

        <div class="relative mt-16 max-w-lg">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-[11px] font-semibold tracking-wide text-white ring-1 ring-white/25">
                <span class="h-1.5 w-1.5 rounded-full bg-brand-100"></span>
                Built for busy students
            </span>
            <h2 class="mt-5 text-[42px] leading-[1.1] font-bold text-white text-balance xl:text-5xl">
                One calm place for <span class="text-brand-100">everything</span> due.
            </h2>
            <p class="mt-4 max-w-md text-sm leading-relaxed text-white/80">
                Track assignments, org tasks, and personal goals. See your week at a glance, then take the next small step.
            </p>

            <div class="mt-9 grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 text-white">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
                    </span>
                    <p class="mt-3 text-sm font-semibold text-white">Plan your week</p>
                    <p class="mt-1 text-[11px] leading-relaxed text-white/70">Tasks, deadlines, and priorities in one view.</p>
                </div>
                <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15 backdrop-blur-sm">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 text-white">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/><circle cx="12" cy="12" r="3"/></svg>
                    </span>
                    <p class="mt-3 text-sm font-semibold text-white">Stay focused</p>
                    <p class="mt-1 text-[11px] leading-relaxed text-white/70">See progress and keep your momentum going.</p>
                </div>
            </div>
        </div>

        <p class="relative text-[11px] text-white/60">© {{ date('Y') }} EladeMate. Plan gently, finish strong.</p>
    </section>
</div>
@endsection
