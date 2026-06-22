@props(['type' => 'button', 'label' => null])

<button type="{{ $type }}" title="{{ $label }}" aria-label="{{ $label }}" {{ $attributes->class('inline-grid h-10 w-10 place-items-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800') }}>
    {{ $slot }}
</button>
