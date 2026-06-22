<div class="space-y-6">
    @php
        $variants = ['primary', 'info', 'warning', 'success', 'neutral', 'warning', 'danger', 'danger', 'info', 'primary'];
        $icons = ['sales', 'cart', 'expenses', 'activity', 'box', 'stock', 'warning', 'returns', 'users', 'reports'];
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ($cards as $card)
            <x-stat-card
                :title="$card['label']"
                :value="is_numeric($card['value']) ? number_format((float) $card['value'], 2) : $card['value']"
                :variant="$variants[$loop->index] ?? 'primary'"
                :icon="$icons[$loop->index] ?? 'activity'"
                :subtitle="__('common.dashboard')"
            />
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-card class="xl:col-span-2" :title="__('reports.sales_report')" :description="__('dashboard.today_sales_total')">
            @php($max = max(collect($salesTrend)->pluck('sales')->max(), 1))
            <div class="flex h-72 items-end gap-3 rounded-3xl bg-gradient-to-b from-slate-50 to-white p-4 dark:from-white/[0.05] dark:to-transparent">
                @foreach ($salesTrend as $point)
                    <div class="flex h-full flex-1 flex-col justify-end gap-3">
                        <div class="group relative flex flex-1 items-end">
                            <div
                                class="w-full rounded-t-2xl bg-gradient-to-t from-emerald-600 via-teal-400 to-sky-300 shadow-lg shadow-emerald-900/10 transition group-hover:from-emerald-500"
                                style="height: {{ max(($point['sales'] / $max) * 100, 4) }}%"
                            ></div>
                        </div>
                        <span class="truncate text-center text-xs font-medium text-slate-500 dark:text-slate-400">{{ $point['date'] }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>

        <x-card :title="__('sidebar.customer_due_payments')" :description="__('dashboard.customer_due_total')">
            <div class="space-y-3">
                @foreach (array_slice($cards, -2) as $card)
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4 dark:border-white/10 dark:bg-white/[0.04]">
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $card['label'] }}</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950 dark:text-white">{{ number_format((float) $card['value'], 2) }}</p>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        @foreach ([__('dashboard.recent_sales') => $recentSales, __('dashboard.recent_purchases') => $recentPurchases] as $title => $rows)
            <x-card :title="$title">
                <x-table>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                        @forelse ($rows as $row)
                            <tr class="ui-row">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ $row->invoice_no }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ optional($row->sale_date ?? $row->purchase_date ?? $row->created_at)->format('Y-m-d') }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $row->supplier?->name ?? $row->customer?->name ?? __('common.walk_in_customer') }}</td>
                                <td class="px-4 py-3 text-end font-semibold text-slate-950 dark:text-white">{{ number_format($row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td class="p-4"><x-empty-state /></td></tr>
                        @endforelse
                    </tbody>
                </x-table>
            </x-card>
        @endforeach

        <x-card :title="__('dashboard.low_stock_products')" :description="__('sidebar.low_stock')">
            <div class="space-y-3">
                @forelse ($lowStockProducts as $product)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-amber-200/70 bg-amber-50/80 px-4 py-3 text-sm dark:border-amber-400/20 dark:bg-amber-400/10">
                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $product->name }}</span>
                        <x-badge variant="warning">{{ $product->current_stock }}</x-badge>
                    </div>
                @empty
                    <x-empty-state />
                @endforelse
            </div>
        </x-card>

        <x-card :title="__('dashboard.expiring_products')" :description="__('sidebar.expiring_products')">
            <div class="space-y-3">
                @forelse ($expiringProducts as $batch)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-rose-200/70 bg-rose-50/70 px-4 py-3 text-sm dark:border-rose-400/20 dark:bg-rose-400/10">
                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $batch->product?->name }} {{ $batch->batch_number }}</span>
                        <x-badge variant="danger">{{ optional($batch->expiry_date)->format('Y-m-d') }}</x-badge>
                    </div>
                @empty
                    <x-empty-state />
                @endforelse
            </div>
        </x-card>
    </div>
</div>
