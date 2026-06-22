@props(['title' => __('common.confirm_delete'), 'message' => __('common.delete_question')])

<div {{ $attributes->class('fixed inset-0 z-50 grid place-items-center bg-slate-950/60 p-4 backdrop-blur-sm') }}>
    <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-white/10 dark:bg-slate-900">
        <h3 class="text-lg font-semibold text-slate-950 dark:text-white">{{ $title }}</h3>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ $message }}</p>
        <div class="mt-6 flex justify-end gap-3">{{ $slot }}</div>
    </div>
</div>
