@extends('pdf.base')

@section('content')
    <div class="title">{{ $title }}</div>
    <p>{{ __('common.name') }}: {{ $person->name }}</p>
    <p>{{ __('common.phone') }}: {{ $person->phone }}</p>
    <table>
        <thead><tr><th>{{ __('common.date') }}</th><th>{{ __('common.total') }}</th><th>{{ __('common.paid') }}</th><th>{{ __('common.due') }}</th></tr></thead>
        <tbody>
            @foreach ($documents as $document)
                <tr><td>{{ optional($document->sale_date ?? $document->purchase_date)->format('Y-m-d') }}</td><td>{{ number_format($document->total, 2) }}</td><td>{{ number_format($document->paid_amount, 2) }}</td><td>{{ number_format($document->due_amount, 2) }}</td></tr>
            @endforeach
            <tr class="total"><td>{{ __('common.total') }}</td><td>{{ number_format($documents->sum('total'), 2) }}</td><td>{{ number_format($payments->sum('amount'), 2) }}</td><td>{{ number_format($documents->sum('due_amount'), 2) }}</td></tr>
        </tbody>
    </table>
@endsection
