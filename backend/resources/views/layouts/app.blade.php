<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="EladeMate is a student task and schedule management app that helps you plan, focus, and thrive.">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'EladeMate') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='18' fill='%23E91E63'/%3E%3Cpath d='M18 33l9 9 19-21' fill='none' stroke='white' stroke-width='7' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#fff8fb] font-sans text-ink-900 antialiased" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-brand-700 focus:shadow-lg">Skip to content</a>

    <x-sidebar />

    <div class="min-h-screen lg:pl-[272px]">
        <x-topbar />

        <main id="main-content" class="mx-auto w-full max-w-[1400px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <x-flash />
            @yield('content')
        </main>

        <footer class="border-t border-brand-100/70 px-4 py-4 text-center text-[11px] text-ink-500 sm:px-6 lg:px-8">
            EladeMate · Student Task &amp; Schedule Management · Built with Laravel &amp; Blade
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
