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

<div class="overflow-hidden bg-white border shadow-2xl rounded-3xl border-slate-200 shadow-slate-900/10 dark:border-slate-800 dark:bg-slate-900">
    <div class="px-6 py-6 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-1">

                <div>
                    <h1 class="text-xl font-bold tracking-tight text-center text-slate-950 dark:text-white">{{ __('common.app_name') }}</h1>
                </div>
            </div>

            <x-icon-button type="button" label="Theme" @click="window.toggleTheme()">
                <svg class="w-5 h-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.36-6.36-1.42 1.42M7.05 16.95l-1.41 1.41m12.72 0-1.42-1.41M7.05 7.05 5.64 5.64"/><circle cx="12" cy="12" r="4"/></svg>
                <svg class="hidden w-5 h-5 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z"/></svg>
            </x-icon-button>
        </div>
    </div>

    <div class="px-6 py-6">
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="login" class="space-y-5">
            <label class="block">
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('Email') }}</span>
                <x-text-input wire:model="form.email" id="email" class="block w-full mt-2" type="email" name="email" required autofocus autocomplete="username" placeholder="ali@gmail.com" />
                <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('Password') }}</span>
                <x-text-input wire:model="form.password" id="password" class="block w-full mt-2" type="password" name="password" required autocomplete="current-password" placeholder="password" />
                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
            </label>

            <div class="flex items-center justify-between gap-3">
                <label for="remember" class="inline-flex items-center">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="rounded shadow-sm border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-950" name="remember">
                    <span class="text-sm font-medium ms-2 text-slate-600 dark:text-slate-300">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm font-semibold transition text-emerald-700 hover:text-emerald-600 dark:text-emerald-300" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <x-button type="submit" class="w-full">
                {{ __('Log in') }}
            </x-button>
        </form>


    </div>
</div>
