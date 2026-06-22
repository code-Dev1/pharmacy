@props(['title', 'subtitle' => null, 'breadcrumbs' => [], 'action' => null])

<div {{ $attributes->class('mb-6') }}>
    <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        @forelse ($breadcrumbs as $label => $href)
            @if ($href)
                <a href="{{ $href }}" wire:navigate class="transition hover:text-emerald-600 dark:hover:text-emerald-300">{{ $label }}</a>
            @else
                <span class="font-medium text-slate-700 dark:text-slate-200">{{ $label }}</span>
            @endif
            @if (! $loop->last)
                <span class="text-slate-300 dark:text-slate-600">/</span>
            @endif
        @empty
            <a href="{{ route('dashboard') }}" wire:navigate class="transition hover:text-emerald-600 dark:hover:text-emerald-300">{{ __('sidebar.dashboard') }}</a>
            <span class="text-slate-300 dark:text-slate-600">/</span>
            <span class="font-medium text-slate-700 dark:text-slate-200">{{ $title }}</span>
        @endforelse
    </div>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ __('common.app_name') }}</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl dark:text-white">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
            @endif
        </div>
        @if ($action)
            <div class="shrink-0">{{ $action }}</div>
        @else
            <div class="h-1 w-28 rounded-full bg-gradient-to-r from-emerald-500 via-teal-400 to-sky-400"></div>
        @endif
    </div>
</div>
