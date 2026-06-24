<div class="space-y-6">
    @if (str_contains($page, 'report'))
        <x-card>
            <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                <div class="grid flex-1 gap-3 md:grid-cols-5">
                    <x-input wire:model.live="filters.from" type="date" />
                    <x-input wire:model.live="filters.to" type="date" />
                    <x-select wire:model.live="filters.payment_status">
                        <option value="">{{ __('common.all') }}</option>
                        <option value="paid">{{ __('common.paid') }}</option>
                        <option value="partial">{{ __('common.partial') }}</option>
                        <option value="due">{{ __('common.due') }}</option>
                    </x-select>
                    <x-select wire:model.live="filters.customer_id">
                        <option value="">{{ __('sales.customer') }}</option>
                        @foreach ($customers as $customer)<option value="{{ $customer->id }}">{{ $customer->name }}</option>@endforeach
                    </x-select>
                    <x-select wire:model.live="filters.supplier_id">
                        <option value="">{{ __('purchases.supplier') }}</option>
                        @foreach ($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach
                    </x-select>
                </div>
                <div class="flex shrink-0 gap-2">
                    <x-button variant="secondary" onclick="window.print()">{{ __('reports.print') }}</x-button>
                    <a href="{{ route('pdf.report', ['type' => $page] + array_filter($filters)) }}" target="_blank">
                        <x-button type="button" variant="outline">{{ __('reports.pdf') }}</x-button>
                    </a>
                </div>
            </div>
        </x-card>
    @endif

    @if ($page === 'customer-due-payments')
        <x-card :title="__('sidebar.customer_due_payments')">
            <form wire:submit="saveCustomerPayment" class="grid gap-3 md:grid-cols-6">
                <x-select wire:model="payment.sale_id"><option value="">{{ __('sales.sale') }}</option>@foreach($salesDue as $sale)<option value="{{ $sale->id }}">{{ $sale->invoice_no }} - {{ $sale->customer?->name }} - {{ number_format($sale->due_amount, 2) }}</option>@endforeach</x-select>
                <x-input wire:model="payment.amount" type="number" step="0.01" placeholder="{{ __('common.amount') }}" />
                <x-input wire:model="payment.payment_date" type="date" />
                <x-select wire:model="payment.payment_method"><option value="cash">{{ __('common.cash') }}</option><option value="bank">{{ __('common.bank') }}</option><option value="card">{{ __('common.card') }}</option><option value="other">{{ __('common.other') }}</option></x-select>
                <x-input wire:model="payment.reference_no" placeholder="{{ __('purchases.reference_no') }}" />
                <x-button type="submit">{{ __('common.save') }}</x-button>
            </form>
        </x-card>
        @php($rows = $customerDueRows)
    @elseif ($page === 'supplier-due-payments')
        <x-card :title="__('sidebar.supplier_due_payments')">
            <form wire:submit="saveSupplierPayment" class="grid gap-3 md:grid-cols-6">
                <x-select wire:model="payment.purchase_id"><option value="">{{ __('purchases.purchase') }}</option>@foreach($purchasesDue as $purchase)<option value="{{ $purchase->id }}">{{ $purchase->invoice_no }} - {{ $purchase->supplier?->name }} - {{ number_format($purchase->due_amount, 2) }}</option>@endforeach</x-select>
                <x-input wire:model="payment.amount" type="number" step="0.01" placeholder="{{ __('common.amount') }}" />
                <x-input wire:model="payment.payment_date" type="date" />
                <x-select wire:model="payment.payment_method"><option value="cash">{{ __('common.cash') }}</option><option value="bank">{{ __('common.bank') }}</option><option value="card">{{ __('common.card') }}</option><option value="other">{{ __('common.other') }}</option></x-select>
                <x-input wire:model="payment.reference_no" placeholder="{{ __('purchases.reference_no') }}" />
                <x-button type="submit">{{ __('common.save') }}</x-button>
            </form>
        </x-card>
        @php($rows = $supplierDueRows)
    @endif

    @if ($page === 'debts')
        <div class="grid gap-6 xl:grid-cols-2">
            <x-card title="کی از ما قرضدار است">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] divide-y divide-slate-200 text-sm dark:divide-slate-800">
                        <thead class="ui-table-head">
                            <tr>
                                <th class="px-4 py-3 text-start">نام مشتری</th>
                                <th class="px-4 py-3 text-start">تعداد بل</th>
                                <th class="px-4 py-3 text-end">مبلغ قرض</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                            @forelse ($customerDueRows as $customer)
                                <tr class="ui-row">
                                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">{{ $customer->name }}</td>
                                    <td class="px-4 py-3">{{ $customer->due_documents_count }}</td>
                                    <td class="px-4 py-3 text-end font-black text-rose-600">{{ number_format((float) $customer->due_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="p-6"><x-empty-state title="هیچ مشتری قرضدار نیست" /></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card title="از کی قرضدار هستیم">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] divide-y divide-slate-200 text-sm dark:divide-slate-800">
                        <thead class="ui-table-head">
                            <tr>
                                <th class="px-4 py-3 text-start">نام تهیه‌کننده</th>
                                <th class="px-4 py-3 text-start">تعداد بل</th>
                                <th class="px-4 py-3 text-end">مبلغ قرض</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                            @forelse ($supplierDueRows as $supplier)
                                <tr class="ui-row">
                                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">{{ $supplier->name }}</td>
                                    <td class="px-4 py-3">{{ $supplier->due_documents_count }}</td>
                                    <td class="px-4 py-3 text-end font-black text-amber-600">{{ number_format((float) $supplier->due_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="p-6"><x-empty-state title="ما به هیچ تهیه‌کننده قرضدار نیستیم" /></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    @endif

    @if ($statement)
        <x-card>
            <div class="grid gap-4 md:grid-cols-4">
                <div><p class="text-sm text-slate-500">{{ __('common.name') }}</p><p class="font-bold">{{ $statement['person']->name }}</p></div>
                <div><p class="text-sm text-slate-500">{{ __('common.phone') }}</p><p class="font-bold">{{ $statement['person']->phone ?? '-' }}</p></div>
                <div><p class="text-sm text-slate-500">{{ __('common.paid') }}</p><p class="font-bold">{{ number_format($statement['payments']->sum('amount'), 2) }}</p></div>
                <div><p class="text-sm text-slate-500">{{ __('common.due') }}</p><p class="font-bold text-red-600">{{ number_format($statement['due'], 2) }}</p></div>
            </div>
        </x-card>
    @endif

    @if ($reportTotals)
        <div class="grid gap-4 md:grid-cols-3">
            @foreach ($reportTotals as $label => $value)
                <x-stat-card :title='__("common.$label") === "common.$label" ? ucfirst($label) : __("common.$label")' :value="number_format((float) $value, 2)" variant="info" icon="reports" />
            @endforeach
        </div>
    @endif

    @unless ($page === 'debts')
    <x-table>
        <thead class="ui-table-head">
            <tr>
                <th class="px-4 py-3 text-start">{{ __('common.name') }}</th>
                <th class="px-4 py-3 text-start">{{ __('common.date') }}</th>
                <th class="px-4 py-3 text-start">{{ __('common.total') }}</th>
                <th class="px-4 py-3 text-start">{{ __('common.status') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
            @forelse ($rows as $row)
                <tr class="ui-row">
                    <td class="px-4 py-3 font-medium">
                        {{ $row->invoice_no ?? $row->product?->name ?? $row->name ?? $row->title ?? '#' . $row->id }}
                        @if (isset($row->due_amount) && $row->due_amount > 0)<x-badge variant="danger" class="ms-2">{{ __('common.due') }}</x-badge>@endif
                    </td>
                    <td class="px-4 py-3">{{ optional($row->sale_date ?? $row->purchase_date ?? $row->expense_date ?? $row->expiry_date ?? $row->created_at)->format('Y-m-d') }}</td>
                    <td class="px-4 py-3">{{ number_format((float) ($row->due_amount ?? $row->total ?? $row->amount ?? $row->remaining_quantity ?? $row->current_stock ?? 0), 2) }}</td>
                    <td class="px-4 py-3">
                        @if (($row->expiry_date ?? null) && $row->expiry_date->isPast())
                            <x-badge variant="danger">{{ __('products.expired') }}</x-badge>
                        @elseif (($row->expiry_date ?? null))
                            <x-badge variant="warning">{{ __('products.near_expiry') }}</x-badge>
                        @elseif (isset($row->due_documents_count))
                            <x-badge variant="danger">{{ $row->due_documents_count }} {{ str_contains($page, 'supplier') ? __('purchases.purchases') : __('sales.sales') }}</x-badge>
                        @elseif ($row->payment_status ?? null)
                            <x-badge variant="{{ $row->payment_status === 'paid' ? 'success' : 'warning' }}">{{ __("common.$row->payment_status") }}</x-badge>
                        @else
                            <x-badge>{{ __('common.active') }}</x-badge>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-6"><x-empty-state /></td></tr>
            @endforelse
        </tbody>
    </x-table>

    @if (method_exists($rows, 'links'))
        {{ $rows->links() }}
    @endif
    @endunless
</div>
