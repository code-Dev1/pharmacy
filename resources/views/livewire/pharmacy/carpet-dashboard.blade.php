<div class="space-y-6">
    <section id="quick-order" class="scroll-mt-24">
        <livewire:pharmacy.sale-create :embedded="true" />
    </section>

    <div class="grid gap-6 xl:grid-cols-[1fr_22rem]">
        <x-card :title="$searchTerm === '' ? 'آخرین فرش‌ها' : 'نتایج جستجو'" :description="$searchTerm === '' ? 'برای یافتن دقیق‌تر از جستجوی داخل فروش سریع استفاده کنید.' : 'عبارت جستجو: ' . $searchTerm">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @forelse ($products as $product)
                    <a href="{{ route('pharmacy.show', ['products', $product->id]) }}" wire:navigate class="group flex min-h-52 flex-col justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:border-emerald-300 hover:shadow-glow dark:border-slate-800 dark:bg-slate-950">
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="truncate text-base font-black text-slate-950 dark:text-white">{{ $product->name }}</h3>
                                    <p class="mt-1 truncate text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $product->category?->name ?? 'بدون دسته' }}</p>
                                </div>
                                <x-badge variant="{{ $product->current_stock <= $product->minimum_stock ? 'warning' : 'success' }}">{{ $product->current_stock }}</x-badge>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                                <div class="rounded-xl bg-slate-50 p-3 dark:bg-white/[0.04]">
                                    <p class="text-slate-400">کد</p>
                                    <p class="mt-1 truncate font-bold text-slate-800 dark:text-slate-100">{{ $product->sku ?: '-' }}</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3 dark:bg-white/[0.04]">
                                    <p class="text-slate-400">بارکد</p>
                                    <p class="mt-1 truncate font-bold text-slate-800 dark:text-slate-100">{{ $product->barcode ?: '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex items-end justify-between gap-3 border-t border-slate-100 pt-4 dark:border-white/10">
                            <div>
                                <p class="text-xs text-slate-400">قیمت فروش</p>
                                <p class="mt-1 text-lg font-black text-slate-950 dark:text-white">{{ number_format((float) $product->sale_price, 2) }}</p>
                            </div>
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 transition group-hover:bg-emerald-600 group-hover:text-white dark:bg-emerald-400/10 dark:text-emerald-300">
                                <svg class="h-5 w-5 {{ \App\Support\Locale::isRtl() ? 'rotate-180' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="md:col-span-2 xl:col-span-4">
                        <x-empty-state title="نتیجه‌ای پیدا نشد" message="نام، کد یا بارکد دیگری را امتحان کنید." />
                    </div>
                @endforelse
            </div>
        </x-card>

        <div class="space-y-6">
            <x-card title="هشدار موجودی" description="طرح‌هایی که نیاز به پیگیری دارند">
                <div class="space-y-3">
                    @forelse ($lowStockProducts as $product)
                        <a href="{{ route('pharmacy.show', ['products', $product->id]) }}" wire:navigate class="flex items-center justify-between gap-3 rounded-2xl border border-amber-200/70 bg-amber-50/80 px-4 py-3 text-sm transition hover:bg-amber-100 dark:border-amber-400/20 dark:bg-amber-400/10 dark:hover:bg-amber-400/15">
                            <span class="min-w-0 truncate font-bold text-slate-800 dark:text-slate-100">{{ $product->name }}</span>
                            <x-badge variant="warning">{{ $product->current_stock }}</x-badge>
                        </a>
                    @empty
                        <x-empty-state />
                    @endforelse
                </div>
            </x-card>

            <x-card title="آخرین عملیات">
                <div class="space-y-4">
                    <div>
                        <p class="mb-2 text-xs font-black uppercase tracking-wide text-slate-400">فروش‌ها</p>
                        <div class="space-y-2">
                            @forelse ($recentSales as $row)
                                <div class="rounded-2xl bg-slate-50 px-3 py-2 dark:bg-white/[0.04]">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="truncate text-sm font-bold text-slate-800 dark:text-slate-100">{{ $row->invoice_no }}</span>
                                        <span class="text-xs font-black text-slate-950 dark:text-white">{{ number_format((float) $row->total, 2) }}</span>
                                    </div>
                                    <p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ $row->customer?->name ?? __('common.walk_in_customer') }}</p>
                                </div>
                            @empty
                                <x-empty-state />
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <p class="mb-2 text-xs font-black uppercase tracking-wide text-slate-400">خریدها</p>
                        <div class="space-y-2">
                            @forelse ($recentPurchases as $row)
                                <div class="rounded-2xl bg-slate-50 px-3 py-2 dark:bg-white/[0.04]">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="truncate text-sm font-bold text-slate-800 dark:text-slate-100">{{ $row->invoice_no }}</span>
                                        <span class="text-xs font-black text-slate-950 dark:text-white">{{ number_format((float) $row->total, 2) }}</span>
                                    </div>
                                    <p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ $row->supplier?->name ?? '-' }}</p>
                                </div>
                            @empty
                                <x-empty-state />
                            @endforelse
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
