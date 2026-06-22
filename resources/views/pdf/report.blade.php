@extends('pdf.base')

@section('content')
    <div class="title">{{ $title }}</div>
    <table>
        <thead><tr><th>{{ __('common.name') }}</th><th>{{ __('common.date') }}</th><th>{{ __('common.total') }}</th><th>{{ __('common.status') }}</th></tr></thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row->invoice_no ?? $row->product?->name ?? $row->name ?? $row->title ?? '#' . $row->id }}</td>
                    <td>{{ optional($row->sale_date ?? $row->purchase_date ?? $row->expense_date ?? $row->expiry_date ?? $row->created_at)->format('Y-m-d') }}</td>
                    <td>{{ number_format((float) ($row->total ?? $row->amount ?? $row->remaining_quantity ?? $row->current_stock ?? 0), 2) }}</td>
                    <td>{{ $row->payment_status ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">{{ __('common.empty') }}</td></tr>
            @endforelse
            @foreach ($totals ?? [] as $key => $value)
                <tr class="total"><td colspan="3">{{ $key }}</td><td>{{ number_format((float) $value, 2) }}</td></tr>
            @endforeach
        </tbody>
    </table>
@endsection
