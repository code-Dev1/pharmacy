<form wire:submit="save" class="space-y-6">
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="space-y-6 min-w-0">
            <x-card :title="__('sidebar.new_purchase')" :description="__('purchases.purchase')">
                <div class="grid gap-5 md:grid-cols-3">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('purchases.supplier') }}</span>
                        <x-select wire:model="form.supplier_id" class="mt-2">
                            <option value="">{{ __('common.all') }}</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </x-select>
                        @error('form.supplier_id')<span class="mt-2 block text-xs text-rose-600">{{ $message }}</span>@enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('purchases.purchase_date') }}</span>
                        <x-input wire:model="form.purchase_date" type="date" class="mt-2" />
                        @error('form.purchase_date')<span class="mt-2 block text-xs text-rose-600">{{ $message }}</span>@enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('common.payment_method') }}</span>
                        <x-select wire:model="form.payment_method" class="mt-2">
                            <option value="cash">{{ __('common.cash') }}</option>
                            <option value="bank">{{ __('common.bank') }}</option>
                            <option value="card">{{ __('common.card') }}</option>
                            <option value="other">{{ __('common.other') }}</option>
                        </x-select>
                    </label>
                </div>
            </x-card>

            <x-card :title="__('purchases.items')">
                <x-slot:footer>
                    <x-button type="button" variant="secondary" wire:click="addItem">{{ __('purchases.add_item') }}</x-button>
                </x-slot:footer>

                @if($showQuickProductForm)
                    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 dark:border-emerald-400/20 dark:bg-emerald-400/10">
                        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">اضافه کردن محصول جدید</h3>
                                <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">اگر نام محصول در لیست نبود، همین‌جا اضافه کنید.</p>
                            </div>
                            <x-button type="button" variant="secondary" class="min-h-8 px-3 py-1 text-xs" wire:click="resetQuickProductForm">{{ __('common.cancel') }}</x-button>
                        </div>
                        <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_14rem_auto] md:items-start">
                            <label class="block">
                                <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ __('common.name') }}</span>
                                <x-input wire:model="quickProduct.name" class="mt-1" />
                                @error('quickProduct.name')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror
                            </label>
                            <label class="block">
                                <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ __('products.barcode') }}</span>
                                <x-input wire:model="quickProduct.barcode" class="mt-1" />
                                @error('quickProduct.barcode')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror
                            </label>
                            <x-button type="button" class="mt-5" wire:click="createQuickProduct">{{ __('common.save') }}</x-button>
                        </div>
                    </div>
                @endif

                <p class="mb-3 text-xs font-medium text-slate-500 dark:text-slate-400">شماره سری/بچ همان شماره تولید روی بسته یا کارتن دوا است؛ اگر ندارد، خالی بگذارید.</p>

                <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full min-w-[1120px] divide-y divide-slate-200 text-sm dark:divide-slate-800">
                        <thead class="bg-slate-50 text-xs font-bold uppercase text-slate-500 dark:bg-white/[0.03] dark:text-slate-400">
                            <tr>
                                <th class="px-3 py-3 text-start">{{ __('products.products') }}</th>
                                <th class="px-3 py-3 text-start">{{ __('products.quantity') }}</th>
                                <th class="px-3 py-3 text-start">{{ __('products.purchase_price') }}</th>
                                <th class="px-3 py-3 text-start">{{ __('products.sale_price') }}</th>
                                <th class="px-3 py-3 text-start">{{ __('purchases.discount') }}</th>
                                <th class="px-3 py-3 text-start">شماره سری/بچ</th>
                                <th class="px-3 py-3 text-start">{{ __('products.expiry_date') }}</th>
                                <th class="px-3 py-3 text-end">{{ __('common.total') }}</th>
                                <th class="px-3 py-3 text-end">{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white dark:divide-slate-800 dark:bg-slate-950">
                            @foreach($items as $index => $item)
                                <tr class="align-top">
                                    <td class="min-w-64 px-3 py-3">
                                        <x-select wire:model="items.{{ $index }}.product_id">
                                            <option value="">{{ __('common.search') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </x-select>
                                        @error("items.$index.product_id")<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror
                                        <button type="button" wire:click="openQuickProductForm({{ $index }})" class="mt-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 dark:text-emerald-300 dark:hover:text-emerald-200">
                                            + اضافه کردن نام جدید
                                        </button>
                                    </td>
                                    <td class="w-24 px-3 py-3">
                                        <x-input wire:model.live="items.{{ $index }}.quantity" type="number" min="1" />
                                        @error("items.$index.quantity")<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror
                                    </td>
                                    <td class="w-32 px-3 py-3">
                                        <x-input wire:model.live="items.{{ $index }}.unit_price" type="number" step="0.01" />
                                        @error("items.$index.unit_price")<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror
                                    </td>
                                    <td class="w-32 px-3 py-3">
                                        <x-input wire:model="items.{{ $index }}.sale_price" type="number" step="0.01" />
                                    </td>
                                    <td class="w-28 px-3 py-3">
                                        <x-input wire:model.live="items.{{ $index }}.discount" type="number" step="0.01" />
                                        @error("items.$index.discount")<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror
                                    </td>
                                    <td class="w-36 px-3 py-3">
                                        <x-input wire:model="items.{{ $index }}.batch_number" />
                                    </td>
                                    <td class="w-40 px-3 py-3">
                                        <x-input wire:model="items.{{ $index }}.expiry_date" type="date" />
                                    </td>
                                    <td class="w-28 px-3 py-3 text-end font-bold text-slate-900 dark:text-white">
                                        {{ number_format(((float) ($item['quantity'] ?: 0) * (float) ($item['unit_price'] ?: 0)) - (float) ($item['discount'] ?: 0), 2) }}
                                    </td>
                                    <td class="w-24 px-3 py-3 text-end">
                                        <x-button type="button" variant="danger" class="min-h-9 px-3 py-1 text-xs" wire:click="removeItem({{ $index }})">{{ __('common.delete') }}</x-button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <div class="space-y-6 xl:sticky xl:top-24 xl:self-start">
            <x-card :title="__('common.total')">
                <div class="space-y-4">
                    <div class="rounded-2xl bg-emerald-50 p-4 dark:bg-emerald-400/10">
                        <span class="text-sm text-emerald-700 dark:text-emerald-300">{{ __('purchases.subtotal') }}</span>
                        <p class="mt-1 text-3xl font-bold text-slate-950 dark:text-white">{{ number_format($subtotal, 2) }}</p>
                    </div>
                    <label class="block"><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('purchases.discount') }}</span><x-input wire:model.live="form.discount" type="number" step="0.01" class="mt-2" /></label>
                    <label class="block"><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('purchases.tax') }}</span><x-input wire:model.live="form.tax" type="number" step="0.01" class="mt-2" /></label>
                    <label class="block"><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('purchases.paid_amount') }}</span><x-input wire:model.live="form.paid_amount" type="number" step="0.01" class="mt-2" /></label>
                    <x-button type="submit" class="w-full">{{ __('common.save') }}</x-button>
                </div>
            </x-card>
        </div>
    </div>
</form>
