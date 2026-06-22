<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav class="sr-only" aria-hidden="true">
    <a href="{{ route('dashboard') }}" wire:navigate>{{ __('common.dashboard') }}</a>
    <a href="{{ route('profile') }}" wire:navigate>{{ __('Profile') }}</a>
    <button wire:click="logout">{{ __('Log Out') }}</button>
</nav>
