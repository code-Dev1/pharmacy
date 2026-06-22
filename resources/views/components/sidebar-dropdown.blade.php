@props(['label', 'icon' => 'circle', 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="relative">
    <button
        type="button"
        title="{{ $label }}"
        @click="open = !open"
        class="group flex w-full items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-emerald-500/15 to-teal-500/10 text-emerald-700 ring-1 ring-emerald-500/15 dark:from-emerald-400/20 dark:to-teal-400/10 dark:text-emerald-200 dark:ring-emerald-400/25' : 'text-slate-700 hover:bg-slate-100/80 dark:text-slate-200 dark:hover:bg-slate-900' }}"
    >
        <x-sidebar-icon :name="$icon" />
        <span class="flex-1 truncate text-start" x-show="!sidebarCollapsed" x-transition.opacity>{{ $label }}</span>
        <svg x-show="!sidebarCollapsed" class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd"/></svg>
    </button>
    <div x-show="open && !sidebarCollapsed" x-transition class="mt-1 space-y-1 ps-9">
        {{ $slot }}
    </div>
</div>
