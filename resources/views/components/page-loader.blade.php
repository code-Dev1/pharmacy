<div
    x-cloak
    x-show="pageLoading"
    x-transition.opacity
    class="fixed inset-0 z-[70] grid place-items-center bg-slate-950/20 backdrop-blur-sm dark:bg-slate-950/45"
>
    <div class="flex items-center gap-3 rounded-2xl border border-white/60 bg-white/90 px-5 py-4 text-sm font-semibold text-slate-800 shadow-2xl shadow-slate-900/15 dark:border-white/10 dark:bg-slate-900/90 dark:text-slate-100">
        <span class="h-5 w-5 animate-spin rounded-full border-2 border-emerald-500 border-t-transparent"></span>
        <span>{{ __('common.loading') }}</span>
    </div>
</div>
