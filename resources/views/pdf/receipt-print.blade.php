<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir ?? \App\Support\Locale::direction() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $title }} - {{ $document->invoice_no }}</title>
    <style>
        @page { size: 80mm auto; margin: 6mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #0f172a; font-family: DejaVu Sans, Tahoma, Arial, sans-serif; font-size: 11px; }
        .receipt { width: 100%; }
        .center { text-align: center; }
        .brand { font-size: 16px; font-weight: 800; }
        .muted { color: #64748b; }
        .line { border-top: 1px dashed #94a3b8; margin: 10px 0; }
        .row { display: flex; justify-content: space-between; gap: 8px; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 5px 0; border-bottom: 1px dashed #e2e8f0; text-align: inherit; vertical-align: top; }
        .num { text-align: end; white-space: nowrap; }
        .total { font-weight: 800; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="center">
            <div class="brand">{{ $setting?->pharmacy_name ?? __('common.app_name') }}</div>
            <div class="muted">{{ $setting?->address }}</div>
            <div class="muted">{{ $setting?->phone }}</div>
        </div>

        <div class="line"></div>

        <div class="row"><span>{{ __('sales.sale') }}</span><strong>{{ $document->invoice_no }}</strong></div>
        <div class="row"><span>{{ __('common.date') }}</span><span>{{ optional($document->sale_date)->format('Y-m-d') }}</span></div>
        <div class="row"><span>{{ __('common.name') }}</span><span>{{ $person?->name ?? __('common.walk_in_customer') }}</span></div>
        <div class="row"><span>{{ __('common.status') }}</span><span>{{ __("common.$document->payment_status") }}</span></div>

        <table>
            <thead>
                <tr>
                    <th>{{ __('products.products') }}</th>
                    <th class="num">{{ __('products.quantity') }}</th>
                    <th class="num">{{ __('common.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>
                            {{ $item->product?->name }}
                            <div class="muted">{{ number_format((float) $item->unit_price, 2) }}</div>
                        </td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">{{ number_format((float) $item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="line"></div>
        <div class="row total"><span>{{ __('common.total') }}</span><span>{{ number_format((float) $document->total, 2) }}</span></div>
        <div class="row"><span>{{ __('common.paid') }}</span><span>{{ number_format((float) $document->paid_amount, 2) }}</span></div>
        <div class="row"><span>{{ __('common.due') }}</span><span>{{ number_format((float) $document->due_amount, 2) }}</span></div>

        @if ($setting?->invoice_footer)
            <div class="line"></div>
            <div class="center muted">{{ $setting->invoice_footer }}</div>
        @endif

        <div class="line"></div>
        <div class="center muted">{{ __('common.created_at') }}: {{ now()->format('Y-m-d') }}</div>
        <div class="center no-print" style="margin-top: 12px;">
            <button type="button" onclick="window.print()">{{ __('reports.print') }}</button>
        </div>
    </div>
</body>
</html>
