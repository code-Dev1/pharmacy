@props(['title' => null, 'description' => null, 'footer' => null])

<section {{ $attributes->class('ui-panel overflow-hidden') }}>
    @if ($title || $description)
        <div class="border-b border-slate-100 px-5 py-4 dark:border-white/10">
            @if ($title)
                <h3 class="text-base font-semibold text-slate-950 dark:text-white">{{ $title }}</h3>
            @endif
            @if ($description)
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
            @endif
        </div>
    @endif
    <div class="p-5">
        {{ $slot }}
    </div>
    @if ($footer)
        <div class="border-t border-slate-100 bg-slate-50/70 px-5 py-4 dark:border-white/10 dark:bg-white/[0.03]">
            {{ $footer }}
        </div>
    @endif
</section>
