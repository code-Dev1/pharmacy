<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script>
            window.applyTheme = function () {
                const storedTheme = localStorage.getItem('theme') || 'light';
                document.documentElement.classList.toggle('dark', storedTheme === 'dark');
            };
            window.setTheme = function (theme) {
                localStorage.setItem('theme', theme);
                window.applyTheme();
            };
            window.toggleTheme = function () {
                window.setTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark');
            };
            window.applyTheme();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased dark:text-slate-100">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-100 dark:bg-slate-950">
            <div>
                <a href="/" wire:navigate>
                    <x-application-logo class="w-20 h-20 fill-current text-emerald-600 dark:text-emerald-300" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white px-6 py-4 shadow-xl shadow-slate-900/10 dark:border-slate-800 dark:bg-slate-900">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
