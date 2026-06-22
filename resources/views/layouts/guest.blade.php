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
    <body class="font-sans text-slate-900 antialiased dark:text-slate-100">
        <main class="min-h-screen bg-slate-50 dark:bg-slate-950">
            <div class="grid min-h-screen lg:grid-cols-2">
                <section class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-12">
                    <div class="w-full max-w-md">
                        {{ $slot }}
                    </div>
                </section>

                <section class="relative hidden min-h-screen overflow-hidden bg-slate-950 lg:block">
                    <img src="{{ asset('images/pharmacy-login-hero.png') }}" alt="Modern pharmacy interior" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-tr from-slate-950 via-slate-950/55 to-emerald-950/10"></div>
                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-slate-950 via-slate-950/70 to-transparent"></div>

                    <div class="relative flex h-full flex-col justify-between p-12 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 shadow-lg shadow-emerald-500/25">
                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 21h8"/><path d="M12 17V3"/><path d="M7 8h10"/><path d="M5 13h14"/></svg>
                                </div>
                                <div>
                                    <p class="text-lg font-bold">{{ __('common.app_name') }}</p>
                                    <p class="text-sm text-emerald-100/80">Premium pharmacy operations</p>
                                </div>
                            </div>
                            <div class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-semibold backdrop-blur">
                                Secure ERP
                            </div>
                        </div>

                        <div>
                            <div class="max-w-xl">
                                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-200">Pharmacy ERP</p>
                                <h1 class="mt-4 text-5xl font-bold leading-tight tracking-tight">A calm workspace for pharmacy sales, stock, and daily operations.</h1>
                                <p class="mt-5 max-w-lg text-base leading-7 text-slate-200">Monitor inventory, expiries, purchases, dues, and reports from one polished dashboard.</p>
                            </div>

                            <div class="mt-8 grid grid-cols-3 gap-3">
                                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                                    <p class="text-2xl font-bold">12</p>
                                    <p class="mt-1 text-xs text-slate-300">Demo products</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                                    <p class="text-2xl font-bold">10</p>
                                    <p class="mt-1 text-xs text-slate-300">Demo sales</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                                    <p class="text-2xl font-bold">43</p>
                                    <p class="mt-1 text-xs text-slate-300">Stock moves</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
