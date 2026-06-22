@props(['message' => session('toast'), 'variant' => 'success'])

@if ($message)
    <div {{ $attributes->class('rounded-2xl border border-emerald-200 bg-emerald-50/95 px-4 py-3 text-sm font-semibold text-emerald-800 shadow-lg shadow-emerald-950/5 backdrop-blur dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-200') }}>
        {{ $message }}
    </div>
@endif
