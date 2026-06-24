@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white/90 px-4 py-3 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-300 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-600">{{ __('Previous') }}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" wire:navigate rel="prev" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-emerald-50 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-emerald-400/10 dark:hover:text-emerald-200">{{ __('Previous') }}</a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" wire:navigate rel="next" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-emerald-50 hover:text-emerald-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 dark:hover:bg-emerald-400/10 dark:hover:text-emerald-200">{{ __('Next') }}</a>
        @else
            <span class="inline-flex items-center rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-300 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-600">{{ __('Next') }}</span>
        @endif
    </nav>
@endif
