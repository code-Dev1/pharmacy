<form wire:submit="save" class="space-y-6">
    <div class="grid gap-6 lg:grid-cols-[22rem_1fr]">
        <x-card :title="__('sidebar.pos')" :description="__('sales.search_product')" class="lg:sticky lg:top-24 lg:self-start">
            <x-input wire:model.live.debounce.300ms="productSearch" type="search" placeholder="{{ __('sales.search_product') }}" />
            <div class="mt-4 max-h-[34rem] space-y-2 overflow-y-auto pe-1">
                @forelse($products as $product)
                    <button type="button" wire:click="addProduct({{ $product->id }})" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-3 text-start shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-200 hover:bg-emerald-50/50 dark:border-white/10 dark:bg-white/[0.04] dark:hover:bg-emerald-400/10">
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $product->name }}</span>
                        <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">{{ __('sales.available') }}: {{ $product->current_stock }} | {{ number_format($product->sale_price, 2) }}</span>
                    </button>
                @empty
                    <x-empty-state />
                @endforelse
            </div>
        </x-card>

        <div class="space-y-6">
            <x-card :title="__('sales.sale')" :description="__('common.payment_method')">
                <div class="grid gap-5 md:grid-cols-3">
                    <label><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('sales.customer') }}</span><x-select wire:model="form.customer_id" class="mt-2"><option value="">{{ __('common.walk_in_customer') }}</option>@foreach($customers as $customer)<option value="{{ $customer->id }}">{{ $customer->name }}</option>@endforeach</x-select></label>
                    <label><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('sales.sale_date') }}</span><x-input wire:model="form.sale_date" type="datetime-local" class="mt-2" /></label>
                    <label><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('common.payment_method') }}</span><x-select wire:model="form.payment_method" class="mt-2"><option value="cash">{{ __('common.cash') }}</option><option value="bank">{{ __('common.bank') }}</option><option value="card">{{ __('common.card') }}</option><option value="other">{{ __('common.other') }}</option></x-select></label>
                </div>
            </x-card>

            <x-card :title="__('sales.cart')">
                @error('cart')<div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-300">{{ $message }}</div>@enderror
                <x-table>
                    <thead class="ui-table-head"><tr><th class="px-3 py-2 text-start">{{ __('products.products') }}</th><th class="px-3 py-2 text-start">{{ __('products.quantity') }}</th><th class="px-3 py-2 text-start">{{ __('products.sale_price') }}</th><th class="px-3 py-2 text-start">{{ __('purchases.discount') }}</th><th></th></tr></thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                        @forelse($items as $index => $item)
                            <tr class="ui-row">
                                <td class="px-3 py-2 font-semibold text-slate-900 dark:text-white">{{ $item['name'] }}</td>
                                <td class="px-3 py-2"><x-input wire:model.live="items.{{ $index }}.quantity" type="number" min="1" class="w-24" /></td>
                                <td class="px-3 py-2"><x-input wire:model.live="items.{{ $index }}.unit_price" type="number" step="0.01" class="w-28" /></td>
                                <td class="px-3 py-2"><x-input wire:model.live="items.{{ $index }}.discount" type="number" step="0.01" class="w-24" /></td>
                                <td class="px-3 py-2"><x-button type="button" variant="danger" class="min-h-8 px-3 py-1 text-xs" wire:click="removeItem({{ $index }})">{{ __('common.delete') }}</x-button></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4"><x-empty-state /></td></tr>
                        @endforelse
                    </tbody>
                </x-table>
            </x-card>

            <x-card :title="__('common.total')">
                <div class="grid gap-5 md:grid-cols-4">
                    <div class="rounded-2xl bg-emerald-50 p-4 dark:bg-emerald-400/10"><span class="text-sm text-emerald-700 dark:text-emerald-300">{{ __('sales.subtotal') }}</span><p class="mt-1 text-2xl font-bold text-slate-950 dark:text-white">{{ number_format($subtotal, 2) }}</p></div>
                    <label><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('sales.discount') }}</span><x-input wire:model.live="form.discount" type="number" step="0.01" class="mt-2" /></label>
                    <label><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('sales.tax') }}</span><x-input wire:model.live="form.tax" type="number" step="0.01" class="mt-2" /></label>
                    <label><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('sales.paid_amount') }}</span><x-input wire:model.live="form.paid_amount" type="number" step="0.01" class="mt-2" /></label>
                </div>
            </x-card>

            <div class="sticky bottom-0 z-20 flex justify-end border-t border-slate-200 bg-white/85 py-4 backdrop-blur dark:border-white/10 dark:bg-slate-950/85">
                <x-button type="submit">{{ __('common.save') }}</x-button>
            </div>
        </div>
    </div>
</form>
