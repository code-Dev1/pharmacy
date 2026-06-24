<div class="space-y-6">
    @if (($config['report'] ?? false))
        @php
            $cost = \App\Models\SaleItem::query()->with('product')->get()->sum(fn ($item) => $item->quantity * ($item->product?->purchase_price ?? 0));
            $cards = [
                __('reports.sales_report') => \App\Models\Sale::sum('total'),
                __('reports.purchase_report') => \App\Models\Purchase::sum('total'),
                __('expenses.total_expenses') => \App\Models\Expense::sum('amount'),
                __('reports.cost_of_sold_items') => $cost,
                __('reports.gross_profit') => \App\Models\Sale::sum('total') - $cost,
                __('reports.net_profit') => \App\Models\Sale::sum('total') - $cost - \App\Models\Expense::sum('amount'),
            ];
        @endphp
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($cards as $label => $value)
                <x-stat-card :title="$label" :value="number_format((float) $value, 2)" variant="info" icon="reports" />
            @endforeach
        </div>
        <x-card :title="__('reports.stock_report')">
            <x-table>
                <thead class="ui-table-head">
                    <tr>
                        <th class="px-4 py-3 text-start">{{ __('common.name') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('products.current_stock') }}</th>
                        <th class="px-4 py-3 text-start">{{ __('products.minimum_stock') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                    @forelse (\App\Models\Product::with('productBatches')->limit(25)->get() as $product)
                        <tr class="ui-row">
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $product->current_stock }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $product->minimum_stock }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-4"><x-empty-state /></td></tr>
                    @endforelse
                </tbody>
            </x-table>
        </x-card>
    @elseif ($action === 'index')
        <x-card>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="grid flex-1 gap-3 md:grid-cols-3">
                    <x-input wire:model.live.debounce.400ms="search" type="search" placeholder="{{ __('common.search') }}" />
                    @if (array_key_exists('is_active', $fields))
                        <x-select wire:model.live="status">
                            <option value="">{{ __('common.all') }}</option>
                            <option value="active">{{ __('common.active') }}</option>
                            <option value="inactive">{{ __('common.inactive') }}</option>
                        </x-select>
                    @endif
                </div>
                @if (!($config['readonly'] ?? false) && !($config['report'] ?? false) && $module !== 'settings')
                    <a href="{{ route('pharmacy.create', $module) }}" wire:navigate>
                        <x-button type="button">{{ __('common.create') }}</x-button>
                    </a>
                @endif
            </div>
        </x-card>

        <x-table>
            <thead class="ui-table-head">
                <tr>
                    <th class="px-4 py-3 text-start">{{ __('common.name') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('common.status') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('common.created_at') }}</th>
                    <th class="px-4 py-3 text-start">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                @forelse ($records as $row)
                    <tr class="ui-row">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900 dark:text-white">
                                {{ $row->name ?? $row->title ?? $row->invoice_no ?? $row->batch_number ?? $row->return_no ?? $row->adjustment_no ?? $row->action ?? '#' . $row->id }}
                            </div>
                            @if ($module === 'products')
                                <x-badge variant="neutral" class="mt-2">{{ __('products.current_stock') }}: {{ $row->current_stock }}</x-badge>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if (isset($row->is_active))
                                <x-badge :variant="$row->is_active ? 'success' : 'neutral'">{{ $row->is_active ? __('common.active') : __('common.inactive') }}</x-badge>
                            @elseif (isset($row->payment_status))
                                <x-badge :variant="$row->payment_status === 'paid' ? 'success' : ($row->payment_status === 'due' ? 'danger' : 'warning')">{{ __("common.$row->payment_status") }}</x-badge>
                            @elseif (isset($row->type))
                                <x-badge variant="info">{{ __("stock.$row->type") }}</x-badge>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ optional($row->created_at)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('pharmacy.show', [$module, $row->id]) }}" wire:navigate><x-button type="button" variant="secondary" class="min-h-8 px-3 py-1 text-xs">{{ __('common.show') }}</x-button></a>
                                @if (!($config['readonly'] ?? false))
                                    <a href="{{ route('pharmacy.edit', [$module, $row->id]) }}" wire:navigate><x-button type="button" variant="secondary" class="min-h-8 px-3 py-1 text-xs">{{ __('common.edit') }}</x-button></a>
                                    <x-button type="button" variant="danger" class="min-h-8 px-3 py-1 text-xs" wire:click="confirmDelete({{ $row->id }})">{{ __('common.delete') }}</x-button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-4"><x-empty-state /></td></tr>
                @endforelse
            </tbody>
        </x-table>
        <div class="ui-panel px-4 py-3">{{ $records->links() }}</div>
    @elseif (in_array($action, ['create', 'edit'], true))
        <form wire:submit="save" class="ui-panel w-full overflow-hidden">
            <div class="border-b border-slate-100 px-5 py-4 dark:border-white/10">
                <h3 class="font-semibold text-slate-950 dark:text-white">{{ $this->title }}</h3>
            </div>
            <div class="grid gap-5 p-5 md:grid-cols-2">
                @foreach ($fields as $name => $field)
                    <label class="{{ ($field['type'] ?? 'text') === 'textarea' ? 'md:col-span-2' : '' }} block">
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __($field['label']) }}</span>
                        @if (($field['type'] ?? 'text') === 'textarea')
                            <x-textarea wire:model="form.{{ $name }}" rows="4" class="mt-2" />
                        @elseif (($field['type'] ?? 'text') === 'select')
                            <x-select wire:model="form.{{ $name }}" class="mt-2">
                                <option value="">{{ __('common.all') }}</option>
                                @foreach ($this->options[$field['options']] ?? [] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </x-select>
                        @elseif (($field['type'] ?? 'text') === 'checkbox')
                            <div class="mt-3 flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-white/10 dark:bg-white/[0.04]">
                                <input wire:model="form.{{ $name }}" type="checkbox" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm text-slate-600 dark:text-slate-300">{{ __('common.active') }}</span>
                            </div>
                        @else
                            <x-input wire:model="form.{{ $name }}" type="{{ $field['type'] ?? 'text' }}" step="0.01" class="mt-2" />
                        @endif
                        @error("form.$name") <span class="mt-2 block text-xs font-medium text-rose-600 dark:text-rose-300">{{ $message }}</span> @enderror
                    </label>
                @endforeach
            </div>
            <div class="sticky bottom-0 flex gap-3 border-t border-slate-100 bg-white/90 px-5 py-4 backdrop-blur dark:border-white/10 dark:bg-slate-900/90">
                <x-button type="submit">{{ __('common.save') }}</x-button>
                <a href="{{ route('pharmacy.index', $module) }}" wire:navigate><x-button type="button" variant="secondary">{{ __('common.cancel') }}</x-button></a>
            </div>
        </form>
    @elseif ($action === 'show')
        <x-card class="w-full" :title="$this->title">
            <dl class="grid gap-4 md:grid-cols-2">
                @foreach ($record->getAttributes() as $key => $value)
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4 dark:border-white/10 dark:bg-white/[0.04]">
                        <dt class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">{{ __("common.$key") === "common.$key" ? str_replace('_', ' ', $key) : __("common.$key") }}</dt>
                        <dd class="mt-2 break-words text-sm font-medium text-slate-900 dark:text-white">{{ is_bool($value) ? ($value ? __('common.yes') : __('common.no')) : ($value ?? '-') }}</dd>
                    </div>
                @endforeach
            </dl>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('pharmacy.index', str_contains($module, 'returns') ? 'returns' : $module) }}" wire:navigate><x-button type="button" variant="secondary">{{ __('common.back') }}</x-button></a>
                @if ($module === 'sales')
                    <a href="{{ route('pdf.sale', $record) }}" target="_blank"><x-button type="button" variant="outline">{{ __('reports.pdf') }}</x-button></a>
                @elseif ($module === 'purchases')
                    <a href="{{ route('pdf.purchase', $record) }}" target="_blank"><x-button type="button" variant="outline">{{ __('reports.pdf') }}</x-button></a>
                @elseif ($module === 'customers')
                    <a href="{{ route('advanced.page', ['customer-statement', $record->id]) }}" wire:navigate><x-button type="button" variant="secondary">{{ __('reports.customer_statement') }}</x-button></a>
                    <a href="{{ route('pdf.customer.statement', $record) }}" target="_blank"><x-button type="button" variant="outline">{{ __('reports.pdf') }}</x-button></a>
                @elseif ($module === 'suppliers')
                    <a href="{{ route('advanced.page', ['supplier-statement', $record->id]) }}" wire:navigate><x-button type="button" variant="secondary">{{ __('reports.supplier_statement') }}</x-button></a>
                    <a href="{{ route('pdf.supplier.statement', $record) }}" target="_blank"><x-button type="button" variant="outline">{{ __('reports.pdf') }}</x-button></a>
                @endif
                @if (!($config['readonly'] ?? false))
                    <a href="{{ route('pharmacy.edit', [$module, $record->id]) }}" wire:navigate><x-button type="button">{{ __('common.edit') }}</x-button></a>
                @endif
            </div>
        </x-card>
    @endif

    @if ($deleteId)
        <div class="fixed inset-0 z-50 grid place-items-center bg-slate-950/60 p-4 backdrop-blur-sm">
            <div class="ui-panel w-full max-w-md overflow-hidden">
                <div class="border-b border-slate-100 px-5 py-4 dark:border-white/10">
                    <h3 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('common.confirm_delete') }}</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('common.delete_question') }}</p>
                </div>
                <div class="flex justify-end gap-3 p-5">
                    <x-button type="button" variant="secondary" wire:click="$set('deleteId', null)">{{ __('common.cancel') }}</x-button>
                    <x-button type="button" variant="danger" wire:click="delete">{{ __('common.delete') }}</x-button>
                </div>
            </div>
        </div>
    @endif
</div>
