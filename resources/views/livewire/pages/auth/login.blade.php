<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/10 dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-100 px-6 py-6 dark:border-slate-800">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white shadow-lg shadow-emerald-500/25">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 21h8"/><path d="M12 17V3"/><path d="M7 8h10"/><path d="M5 13h14"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-950 dark:text-white">{{ __('common.app_name') }}</h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Sign in to your pharmacy workspace</p>
                </div>
            </div>

            <x-icon-button type="button" label="Theme" @click="window.toggleTheme()">
                <svg class="h-5 w-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.36-6.36-1.42 1.42M7.05 16.95l-1.41 1.41m12.72 0-1.42-1.41M7.05 7.05 5.64 5.64"/><circle cx="12" cy="12" r="4"/></svg>
                <svg class="hidden h-5 w-5 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z"/></svg>
            </x-icon-button>
        </div>
    </div>

    <div class="px-6 py-6">
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="login" class="space-y-5">
            <label class="block">
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('Email') }}</span>
                <x-text-input wire:model="form.email" id="email" class="mt-2 block w-full" type="email" name="email" required autofocus autocomplete="username" placeholder="test@example.com" />
                <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('Password') }}</span>
                <x-text-input wire:model="form.password" id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="current-password" placeholder="password" />
                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
            </label>

            <div class="flex items-center justify-between gap-3">
                <label for="remember" class="inline-flex items-center">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-950" name="remember">
                    <span class="ms-2 text-sm font-medium text-slate-600 dark:text-slate-300">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm font-semibold text-emerald-700 transition hover:text-emerald-600 dark:text-emerald-300" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <x-button type="submit" class="w-full">
                {{ __('Log in') }}
            </x-button>
        </form>

        <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-200">
            <p class="font-semibold">Demo account</p>
            <p class="mt-1">test@example.com / password</p>
        </div>
    </div>
</div>
