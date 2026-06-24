@props(['message' => session('toast'), 'variant' => session('toast_variant', 'success')])

@php
    $initialToast = $message ? ['message' => $message, 'variant' => $variant] : null;
@endphp

<div
    x-data="{
        toasts: @js($initialToast ? [$initialToast] : []),
        init() {
            this.toasts = this.toasts.map((toast) => ({ ...toast, id: toast.id || Date.now() + Math.random() }));
            this.toasts.forEach((toast) => setTimeout(() => this.removeToast(toast.id), 4500));
        },
        pushToast(event) {
            const detail = event.detail || {};
            const toast = {
                id: Date.now() + Math.random(),
                message: detail.message || detail[0]?.message || @js(__('common.saved')),
                variant: detail.variant || detail[0]?.variant || 'success',
            };

            this.toasts.push(toast);
            setTimeout(() => this.toasts = this.toasts.filter((item) => item.id !== toast.id), 4500);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter((item) => item.id !== id);
        },
    }"
    x-on:notify.window="pushToast($event)"
    class="pointer-events-none fixed end-4 top-24 z-[80] flex w-[min(24rem,calc(100vw-2rem))] flex-col gap-3"
>
    <template x-for="toast in toasts" :key="toast.id || toast.message">
        <div
            x-show="true"
            x-transition.opacity.scale.95
            class="pointer-events-auto rounded-2xl border px-4 py-3 text-sm font-semibold shadow-2xl backdrop-blur"
            :class="{
                'border-emerald-200 bg-emerald-50/95 text-emerald-800 shadow-emerald-950/5 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-200': toast.variant === 'success',
                'border-rose-200 bg-rose-50/95 text-rose-800 shadow-rose-950/5 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-200': toast.variant === 'danger',
                'border-amber-200 bg-amber-50/95 text-amber-800 shadow-amber-950/5 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-200': toast.variant === 'warning',
                'border-sky-200 bg-sky-50/95 text-sky-800 shadow-sky-950/5 dark:border-sky-400/20 dark:bg-sky-400/10 dark:text-sky-200': toast.variant === 'info',
            }"
        >
            <div class="flex items-start justify-between gap-3">
                <span x-text="toast.message"></span>
                <button type="button" class="text-current/60 transition hover:text-current" x-on:click="removeToast(toast.id)" aria-label="Close">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/></svg>
                </button>
            </div>
        </div>
    </template>
</div>
