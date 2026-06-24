<form wire:submit="save" class="space-y-6">
    <div class="grid gap-6 xl:grid-cols-[1fr_22rem]">
        <div class="space-y-6">
            <x-card :title="__('sidebar.new_purchase')" :description="__('purchases.purchase')">
                <div class="grid gap-5 md:grid-cols-3">
                    <label>
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('purchases.supplier') }}</span>
                        <x-select wire:model="form.supplier_id" class="mt-2"><option value="">{{ __('common.all') }}</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach</x-select>
                        @error('form.supplier_id')<span class="block mt-2 text-xs text-rose-600">{{ $message }}</span>@enderror
                    </label>
                    <label><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('purchases.purchase_date') }}</span><x-input wire:model="form.purchase_date" type="date" class="mt-2" /></label>
                    <label><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('common.payment_method') }}</span><x-select wire:model="form.payment_method" class="mt-2"><option value="cash">{{ __('common.cash') }}</option><option value="bank">{{ __('common.bank') }}</option><option value="card">{{ __('common.card') }}</option><option value="other">{{ __('common.other') }}</option></x-select></label>
                </div>
            </x-card>

            <x-card :title="__('purchases.items')">
                <x-slot:footer>
                    <x-button type="button" variant="secondary" wire:click="addItem">{{ __('purchases.add_item') }}</x-button>
                </x-slot:footer>
                <x-table>
                    <thead class="ui-table-head"><tr><th class="px-3 py-2 text-start">{{ __('products.products') }}</th><th class="px-3 py-2 text-start">{{ __('products.quantity') }}</th><th class="px-3 py-2 text-start">{{ __('products.purchase_price') }}</th><th class="px-3 py-2 text-start">{{ __('products.sale_price') }}</th><th class="px-3 py-2 text-start">{{ __('purchases.discount') }}</th><th class="px-3 py-2 text-start">{{ __('products.batch_number') }}</th><th class="px-3 py-2 text-start">{{ __('products.expiry_date') }}</th><th></th></tr></thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                        @foreach($items as $index => $item)
                            <tr class="ui-row">
                                <td class="px-3 py-2"><x-select wire:model="items.{{ $index }}.product_id" class="min-w-52"><option value="">{{ __('common.search') }}</option>@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name }}</option>@endforeach</x-select></td>
                                <td class="px-3 py-2"><x-input wire:model.live="items.{{ $index }}.quantity" type="number" min="1" class="w-24" /></td>
                                <td class="px-3 py-2"><x-input wire:model.live="items.{{ $index }}.unit_price" type="number" step="0.01" class="w-28" /></td>
                                <td class="px-3 py-2"><x-input wire:model="items.{{ $index }}.sale_price" type="number" step="0.01" class="w-28" /></td>
                                <td class="px-3 py-2"><x-input wire:model.live="items.{{ $index }}.discount" type="number" step="0.01" class="w-24" /></td>
                                <td class="px-3 py-2"><x-input wire:model="items.{{ $index }}.batch_number" class="w-32" /></td>
                                <td class="px-3 py-2"><x-input wire:model="items.{{ $index }}.expiry_date" type="date" class="w-40" /></td>
                                <td class="px-3 py-2"><x-button type="button" variant="danger" class="px-3 py-1 text-xs min-h-8" wire:click="removeItem({{ $index }})">{{ __('common.delete') }}</x-button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </x-card>
        </div>

        <div class="space-y-6 xl:sticky xl:top-24 xl:self-start">
            <x-card :title="__('common.total')">
                <div class="space-y-4">
                    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-400/10"><span class="text-sm text-emerald-700 dark:text-emerald-300">{{ __('purchases.subtotal') }}</span><p class="mt-1 text-3xl font-bold text-slate-950 dark:text-white">{{ number_format($subtotal, 2) }}</p></div>
                    <label><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('purchases.discount') }}</span><x-input wire:model.live="form.discount" type="number" step="0.01" class="mt-2" /></label>
                    <label><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('purchases.tax') }}</span><x-input wire:model.live="form.tax" type="number" step="0.01" class="mt-2" /></label>
                    <label><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('purchases.paid_amount') }}</span><x-input wire:model.live="form.paid_amount" type="number" step="0.01" class="mt-2" /></label>
                    <x-button type="submit" class="w-full">{{ __('common.save') }}</x-button>
                </div>
            </x-card>
        </div>
    </div>
</form>
