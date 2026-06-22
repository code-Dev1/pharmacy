@props(['title' => null, 'message' => null])

<div {{ $attributes->class('grid place-items-center rounded-3xl border border-dashed border-slate-300 bg-gradient-to-b from-slate-50 to-white px-6 py-12 text-center dark:border-white/10 dark:from-white/[0.06] dark:to-white/[0.02]') }}>
    <div>
        <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl border border-slate-200 bg-white text-slate-500 shadow-sm dark:border-white/10 dark:bg-white/10 dark:text-slate-300">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
        </div>
        <p class="mt-4 font-semibold text-slate-800 dark:text-slate-100">{{ $title ?? __('common.empty') }}</p>
        @if ($message)
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $message }}</p>
        @endif
    </div>
</div>
