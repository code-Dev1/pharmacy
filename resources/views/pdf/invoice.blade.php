@extends('pdf.base')

@section('content')
    <div class="title">{{ $title }} - {{ $document->invoice_no }}</div>
    <p>{{ __('common.name') }}: {{ $person?->name ?? __('common.walk_in_customer') }}</p>
    <p>{{ __('common.status') }}: {{ __("common.$document->payment_status") }}</p>
    <table>
        <thead><tr><th>{{ __('products.products') }}</th><th>{{ __('products.quantity') }}</th><th>{{ __('products.sale_price') }}</th><th>{{ __('common.total') }}</th></tr></thead>
        <tbody>
            @foreach ($items as $item)
                <tr><td>{{ $item->product?->name }}</td><td>{{ $item->quantity }}</td><td>{{ number_format($item->unit_price, 2) }}</td><td>{{ number_format($item->total, 2) }}</td></tr>
            @endforeach
            <tr class="total"><td colspan="3">{{ __('common.total') }}</td><td>{{ number_format($document->total, 2) }}</td></tr>
            <tr><td colspan="3">{{ __('common.paid') }}</td><td>{{ number_format($document->paid_amount, 2) }}</td></tr>
            <tr><td colspan="3">{{ __('common.due') }}</td><td>{{ number_format($document->due_amount, 2) }}</td></tr>
        </tbody>
    </table>
    <p>{{ $setting?->invoice_footer }}</p>
@endsection
