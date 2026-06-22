@props(['href' => '#', 'active' => false, 'icon' => null])

<a href="{{ $href }}" wire:navigate {{ $attributes->class([
    'group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition-all duration-200',
    'bg-white text-emerald-700 shadow-sm ring-1 ring-emerald-500/20 dark:bg-emerald-500/15 dark:text-emerald-200 dark:ring-emerald-400/25' => $active,
    'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-200 dark:hover:bg-slate-900 dark:hover:text-white' => ! $active,
]) }}>
    <x-sidebar-icon :name="$icon ?? 'circle'" />
    <span class="truncate" x-show="!sidebarCollapsed" x-transition.opacity>{{ $slot }}</span>
</a>
