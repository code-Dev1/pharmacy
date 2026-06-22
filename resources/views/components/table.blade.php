<div {{ $attributes->class('ui-panel overflow-hidden') }}>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-sm dark:divide-white/10">
            {{ $slot }}
        </table>
    </div>
</div>
