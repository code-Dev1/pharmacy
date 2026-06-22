@props(['variant' => 'primary', 'type' => 'button'])

@php
    $classes = [
        'primary' => 'bg-gradient-to-b from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-600/20 hover:-translate-y-0.5 hover:from-emerald-400 hover:to-emerald-600 focus:ring-emerald-500',
        'secondary' => 'border border-slate-200 bg-white text-slate-800 hover:-translate-y-0.5 hover:bg-slate-50 focus:ring-slate-400 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-white/10',
        'success' => 'bg-green-600 text-white shadow-lg shadow-green-600/20 hover:-translate-y-0.5 hover:bg-green-700 focus:ring-green-500',
        'danger' => 'bg-rose-600 text-white shadow-lg shadow-rose-600/20 hover:-translate-y-0.5 hover:bg-rose-700 focus:ring-rose-500',
        'outline' => 'border border-emerald-200 bg-emerald-50/40 text-emerald-700 hover:-translate-y-0.5 hover:bg-emerald-50 focus:ring-emerald-500 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-300',
        'ghost' => 'bg-transparent text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-white/10',
    ][$variant] ?? '';
@endphp

<button type="{{ $type }}" {{ $attributes->class("inline-flex min-h-10 items-center justify-center gap-2 rounded-2xl px-4 py-2 text-sm font-semibold transition duration-200 focus:outline-none focus:ring-4 focus:ring-offset-0 disabled:pointer-events-none disabled:opacity-50 $classes") }}>
    {{ $slot }}
</button>
