<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir ?? \App\Support\Locale::direction() }}">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; }
        .header { border-bottom: 2px solid #10b981; padding-bottom: 12px; margin-bottom: 18px; }
        .brand { font-size: 22px; font-weight: bold; color: #047857; }
        .muted { color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background: #f1f5f9; text-align: inherit; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; }
        .total { font-weight: bold; background: #ecfdf5; }
        .title { font-size: 18px; font-weight: bold; margin: 12px 0; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">{{ $setting?->pharmacy_name ?? __('common.app_name') }}</div>
        <div class="muted">{{ $setting?->address }} {{ $setting?->phone }}</div>
        <div class="muted">{{ __('common.date') }}: {{ now()->format('Y-m-d H:i') }}</div>
    </div>
    @yield('content')
</body>
</html>
