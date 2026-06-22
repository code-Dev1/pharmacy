@props(['show' => false, 'title' => null])

<div x-data="{ show: @js($show) }" x-show="show" x-transition.opacity class="fixed inset-0 z-50 grid place-items-center bg-slate-950/60 p-4 backdrop-blur-sm">
    <div @click.outside="show = false" x-transition.scale.origin.center class="ui-panel w-full max-w-lg overflow-hidden">
        @if ($title)
            <div class="border-b border-slate-100 px-6 py-4 dark:border-white/10">
                <h3 class="text-lg font-semibold text-slate-950 dark:text-white">{{ $title }}</h3>
            </div>
        @endif
        <div class="p-6">{{ $slot }}</div>
    </div>
</div>
