<form wire:submit="save" class="space-y-6">
    <x-card :title="__('stock.stock_adjustments')" :description="__('stock.reason')">
        <div class="grid gap-5 md:grid-cols-3">
            <label><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('stock.adjustment_date') }}</span><x-input wire:model="form.adjustment_date" type="date" class="mt-2" /></label>
            <label><span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('stock.reason') }}</span><x-input wire:model="form.reason" class="mt-2" /></label>
            <x-button type="button" variant="secondary" class="self-end" wire:click="addItem">{{ __('purchases.add_item') }}</x-button>
        </div>
    </x-card>
    <x-table>
        <thead class="ui-table-head"><tr><th class="px-4 py-3 text-start">{{ __('products.batch_number') }}</th><th class="px-4 py-3 text-start">{{ __('stock.system_quantity') }}</th><th class="px-4 py-3 text-start">{{ __('stock.actual_quantity') }}</th></tr></thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
            @foreach($items as $index => $item)
                <tr class="ui-row">
                    <td class="px-4 py-3"><x-select wire:model.live="items.{{ $index }}.product_batch_id"><option value="">{{ __('common.search') }}</option>@foreach($batches as $batch)<option value="{{ $batch->id }}">{{ $batch->product?->name }} - {{ $batch->batch_number ?? '#' . $batch->id }}</option>@endforeach</x-select></td>
                    <td class="px-4 py-3"><x-input wire:model="items.{{ $index }}.system_quantity" type="number" readonly class="w-32 bg-slate-100 dark:bg-white/10" /></td>
                    <td class="px-4 py-3"><x-input wire:model="items.{{ $index }}.actual_quantity" type="number" min="0" class="w-32" /></td>
                </tr>
            @endforeach
        </tbody>
    </x-table>
    <x-button type="submit">{{ __('common.save') }}</x-button>
</form>
