<x-admin-layout :title="__('common.search')">
    <div class="space-y-6">
        <x-card>
            <form method="GET" action="{{ route('global.search') }}" class="flex flex-col gap-3 md:flex-row">
                <x-input name="q" value="{{ $query }}" type="search" placeholder="{{ __('common.search') }}" class="md:flex-1" autofocus />
                <x-button type="submit">{{ __('common.search') }}</x-button>
            </form>
        </x-card>

        @if ($query === '')
            <x-empty-state title="Search the system" message="Search products, invoices, customers, suppliers, batches, expenses, and activity logs." />
        @else
            @php($total = collect($sections)->sum(fn ($rows) => $rows->count()))

            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">
                    {{ $total }} results for <span class="text-emerald-600 dark:text-emerald-300">"{{ $query }}"</span>
                </p>
            </div>

            @if ($total === 0)
                <x-empty-state title="No results" message="Try another product name, invoice number, phone, batch number, or activity keyword." />
            @else
                <div class="grid gap-6 xl:grid-cols-2">
                    @foreach ($sections as $type => $rows)
                        @continue($rows->isEmpty())

                        <x-card :title="str($type)->headline()->toString()">
                            <div class="space-y-2">
                                @foreach ($rows as $item)
                                    <a href="{{ $item['href'] }}" wire:navigate class="block rounded-2xl border border-slate-100 bg-slate-50/70 px-4 py-3 transition hover:-translate-y-0.5 hover:border-emerald-200 hover:bg-emerald-50/70 dark:border-slate-800 dark:bg-slate-950 dark:hover:border-emerald-400/30 dark:hover:bg-emerald-400/10">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ $item['label'] }}</p>
                                                @if ($item['description'])
                                                    <p class="mt-1 line-clamp-2 text-xs text-slate-500 dark:text-slate-400">{{ $item['description'] }}</p>
                                                @endif
                                            </div>
                                            <x-badge variant="neutral">{{ $item['badge'] }}</x-badge>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</x-admin-layout>
