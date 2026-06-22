@props(['title', 'value', 'subtitle' => null, 'variant' => 'primary', 'icon' => 'activity'])

@php
    $palette = [
        'primary' => ['bar' => 'from-emerald-500 to-teal-500', 'icon' => 'text-emerald-700 bg-emerald-50 dark:text-emerald-300 dark:bg-emerald-400/10'],
        'success' => ['bar' => 'from-green-500 to-emerald-500', 'icon' => 'text-green-700 bg-green-50 dark:text-green-300 dark:bg-green-400/10'],
        'warning' => ['bar' => 'from-amber-500 to-orange-500', 'icon' => 'text-amber-700 bg-amber-50 dark:text-amber-300 dark:bg-amber-400/10'],
        'danger' => ['bar' => 'from-rose-500 to-red-500', 'icon' => 'text-rose-700 bg-rose-50 dark:text-rose-300 dark:bg-rose-400/10'],
        'info' => ['bar' => 'from-sky-500 to-cyan-500', 'icon' => 'text-sky-700 bg-sky-50 dark:text-sky-300 dark:bg-sky-400/10'],
        'neutral' => ['bar' => 'from-slate-500 to-zinc-500', 'icon' => 'text-slate-700 bg-slate-50 dark:text-slate-300 dark:bg-white/10'],
    ][$variant] ?? ['bar' => 'from-emerald-500 to-teal-500', 'icon' => 'text-emerald-700 bg-emerald-50 dark:text-emerald-300 dark:bg-emerald-400/10'];
@endphp

<div {{ $attributes->class('ui-panel group relative overflow-hidden p-5 transition duration-200 hover:-translate-y-1 hover:shadow-glow') }}>
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r {{ $palette['bar'] }}"></div>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="truncate text-sm font-medium text-slate-500 dark:text-slate-400">{{ $title }}</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-slate-950 dark:text-white">{{ $value }}</p>
            @if ($subtitle)
                <p class="mt-2 text-xs font-medium text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl {{ $palette['icon'] }}">
            <x-sidebar-icon :name="$icon" class="h-5 w-5" />
        </div>
    </div>
</div>
