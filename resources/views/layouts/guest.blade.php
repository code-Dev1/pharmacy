<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Pharmacy Management System') }}</title>

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

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
<<<<<<< HEAD
    <body class="font-sans antialiased text-slate-900 dark:text-slate-100">
=======
    <body class="font-sans text-slate-900 antialiased dark:text-slate-100">
        <x-toast />
>>>>>>> 0f10fe2b0d1ece7f5695e8fb874cba5580c8090f
        <main class="min-h-screen bg-slate-50 dark:bg-slate-950">
            <div class="grid min-h-screen lg:grid-cols-2">
                <section class="flex items-center justify-center min-h-screen px-4 py-10 sm:px-6 lg:px-12">
                    <div class="w-full max-w-md">
                        {{ $slot }}
                    </div>
                </section>

                <section class="relative hidden min-h-screen overflow-hidden lg:block">
                    <img src="{{ asset('images/pharmacy-login-hero.png') }}" alt="Modern pharmacy interior" class="absolute inset-0 object-cover w-full h-full">
                </section>
            </div>
        </main>
    </body>
</html>
