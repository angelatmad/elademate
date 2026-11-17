@extends('layouts.auth')

@section('title', 'Log in')

@section('content')
<div class="min-h-screen lg:grid lg:grid-cols-[1.05fr_1fr]">
    <section class="relative hidden overflow-hidden bg-gradient-to-br from-[#f43f7f] via-brand-600 to-brand-800 p-10 lg:flex lg:flex-col lg:justify-between xl:p-14" aria-label="Welcome to EladeMate">
        <div class="pointer-events-none absolute -left-24 top-24 h-72 w-72 rounded-full bg-white/10 blur-2xl"></div>
        <div class="pointer-events-none absolute -right-20 bottom-10 h-96 w-96 rounded-full bg-brand-300/20 blur-3xl"></div>
        <div class="pointer-events-none absolute right-16 top-16 h-40 w-40 rounded-full border border-white/20"></div>

        <div class="relative">
            <x-logo light :href="route('login')" />
        </div>

        <div class="relative mt-16 max-w-lg">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3.5 py-1.5 text-[11px] font-semibold tracking-wide text-white ring-1 ring-white/25">
                <span class="h-1.5 w-1.5 rounded-full bg-brand-100"></span>
                Your study life, organized
            </span>
            <h1 class="mt-5 text-[42px] leading-[1.1] font-bold text-white text-balance xl:text-5xl">
                Make every deadline feel <span class="text-brand-100">doable.</span>
            </h1>
            <p class="mt-4 max-w-md text-sm leading-relaxed text-white/80">
                EladeMate keeps your schoolwork, org duties, and personal goals in one calm space — so you can focus on what matters today.
            </p>

            <div class="mt-9 space-y-3">
                <div class="flex items-center gap-3 rounded-2xl bg-white/10 p-3.5 ring-1 ring-white/15 backdrop-blur-sm">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6h11M9 12h11M9 18h11"/><path d="m3 6 1.5 1.5L7 5M3 12l1.5 1.5L7 11M3 18l1.5 1.5L7 17"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-white">Finish HCI final project</p>
                        <p class="text-[11px] text-white/70">School · Due today at 5:00 PM</p>
                    </div>
                    <span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-brand-700">High</span>
                </div>
                <div class="flex items-center gap-3 rounded-2xl bg-white/10 p-3.5 ring-1 ring-white/15 backdrop-blur-sm">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-400/20 text-emerald-100">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-white line-through decoration-white/40">Review OOP 2 notes</p>
                        <p class="text-[11px] text-white/70">Completed · Nice work!</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="relative text-[11px] text-white/60">© {{ date('Y') }} EladeMate. Plan gently, finish strong.</p>
    </section>

    <section class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8">
        <div class="w-full max-w-[420px]">
            <div class="mb-8 flex justify-center lg:hidden">
                <x-logo />
            </div>

            <div class="rounded-3xl border border-brand-100 bg-white p-6 shadow-[0_30px_80px_-40px_rgba(194,24,91,0.45)] sm:p-8">
                <span class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-3 py-1 text-[10px] font-semibold tracking-[0.14em] text-brand-700 uppercase">
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M18.4 5.6l-2.1 2.1M7.7 16.3l-2.1 2.1"/></svg>
                    Welcome back
                </span>
                <h2 class="mt-4 text-[26px] font-bold tracking-tight text-ink-900">Log in to EladeMate</h2>
                <p class="mt-1 text-sm text-ink-500">Pick up where you left off and make today count.</p>

                @if (session('success'))
                    <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-xs font-medium text-emerald-800" role="status">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-xs font-medium text-rose-800" role="alert">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4" novalidate>
                    @csrf

                    <div>
                        <label for="email" class="label">Email address</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-ink-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m4 7 8 6 8-6"/></svg>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                   placeholder="you@example.com"
                                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                                   class="input pl-10">
                        </div>
                        @error('email')
                            <p id="email-error" class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ show: false }">
                        <label for="password" class="label">Password</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-ink-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="3"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                            <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="current-password"
                                   placeholder="Enter your password"
                                   @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                                   class="input pr-12 pl-10">
                            <button type="button" @click="show = !show" :aria-label="show ? 'Hide password' : 'Show password'" class="absolute right-1.5 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-ink-500 transition hover:bg-brand-50 hover:text-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-300">
                                <svg x-show="!show" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="show" x-cloak class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18"/><path d="M10.6 6.2A10.9 10.9 0 0 1 12 6c6.5 0 10 6 10 6a17.8 17.8 0 0 1-3.1 3.8M6.6 6.7C3.6 8.5 2 12 2 12s3.5 7 10 7c1.6 0 3-.4 4.2-1.1"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p id="password-error" class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-0.5">
                        <label for="remember" class="flex cursor-pointer items-center gap-2 text-xs font-medium text-ink-500">
                            <input id="remember" name="remember" value="1" type="checkbox" @checked(old('remember'))
                                   class="h-4 w-4 rounded border-brand-200 text-brand-600 focus:ring-brand-300">
                            Remember me
                        </label>
                        <span class="text-[11px] text-ink-500">Keep your streak going</span>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3">
                        Log in
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                </form>

                <p class="mt-5 text-center text-sm text-ink-500">
                    New to EladeMate?
                    <a href="{{ route('register') }}" class="font-semibold text-brand-700 transition hover:text-brand-800">Create an account</a>
                </p>

                @if (app()->environment('local'))
                    <div class="mt-6 rounded-2xl border border-dashed border-brand-200 bg-brand-50/60 p-3.5">
                        <p class="flex items-center gap-1.5 text-[11px] font-semibold text-brand-800">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
                            Demo account (local only)
                        </p>
                        <p class="mt-1 text-[11px] text-ink-500">Email: <span class="font-semibold text-ink-700">student@elademate.com</span></p>
                        <p class="text-[11px] text-ink-500">Password: <span class="font-semibold text-ink-700">password</span></p>
                    </div>
                @endif
            </div>

            <p class="mt-6 text-center text-[11px] text-ink-500">Protected by secure sessions. Your tasks stay yours.</p>
        </div>
    </section>
</div>
@endsection
