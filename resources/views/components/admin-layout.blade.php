@props(['title' => null])

@php
    $dir = \App\Support\Locale::direction();
    $routeName = request()->route()?->getName();
    $module = request()->route('module');
    $advancedPage = request()->route('page');
    $moduleTitles = [
        'categories' => 'products.categories',
        'products' => 'products.products',
        'batches' => 'products.batches',
        'purchases' => 'purchases.purchases',
        'purchase-payments' => 'purchases.payments',
        'sales' => 'sales.sales',
        'sale-payments' => 'sales.payments',
        'stock-movements' => 'stock.stock_movements',
        'stock-adjustments' => 'stock.stock_adjustments',
        'returns' => 'returns.returns',
        'sale-returns' => 'returns.sale_return',
        'purchase-returns' => 'returns.purchase_return',
        'customers' => 'customers.customers',
        'suppliers' => 'suppliers.suppliers',
        'expense-categories' => 'expenses.expense_categories',
        'expenses' => 'expenses.expenses',
        'reports' => 'sidebar.reports',
        'settings' => 'settings.settings',
        'activity-logs' => 'settings.activity_logs',
    ];
    $advancedTitles = [
        'customer-due-payments' => 'sidebar.customer_due_payments',
        'supplier-due-payments' => 'sidebar.supplier_due_payments',
        'expired-products' => 'sidebar.expired_products',
        'near-expiry-products' => 'sidebar.expiring_products',
        'low-stock-products' => 'sidebar.low_stock',
        'customer-statement' => 'reports.customer_statement',
        'supplier-statement' => 'reports.supplier_statement',
        'sales-report' => 'reports.sales_report',
        'purchase-report' => 'reports.purchase_report',
        'stock-report' => 'reports.stock_report',
        'expiry-report' => 'reports.expiry_report',
        'profit-loss-report' => 'reports.profit_loss_report',
        'customer-due-report' => 'sidebar.customer_due_report',
        'supplier-due-report' => 'sidebar.supplier_due_report',
        'expense-report' => 'reports.expense_report',
    ];
    $fallbackTitle = match ($routeName) {
        'dashboard' => __('common.dashboard'),
        'purchases.create' => __('sidebar.new_purchase'),
        'sales.create' => __('sidebar.pos'),
        'stock-adjustments.create' => __('stock.stock_adjustments'),
        'advanced.page' => __($advancedTitles[$advancedPage] ?? 'sidebar.reports'),
        'pharmacy.index', 'pharmacy.create', 'pharmacy.show', 'pharmacy.edit' => __($moduleTitles[$module] ?? 'common.dashboard'),
        default => __('common.dashboard'),
    };
    $pageTitle = $title ?? ($header ?? $fallbackTitle);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $dir }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ __('common.app_name') }}</title>
        <script>
            window.applyTheme = function () {
                const storedTheme = localStorage.getItem('theme') || 'light';
                document.documentElement.classList.toggle('dark', storedTheme === 'dark');
            };
            window.setTheme = function (theme) {
                localStorage.setItem('theme', theme);
                window.applyTheme();
            };
            window.toggleTheme = function () {
                window.setTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark');
            };
            window.applyTheme();
            document.addEventListener('livewire:navigated', window.applyTheme);
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="{{ \App\Support\Locale::fontClass() }} text-slate-900 antialiased dark:text-slate-100">
        <div
            x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true', pageLoading: false }"
            x-effect="localStorage.setItem('sidebarCollapsed', sidebarCollapsed)"
            x-on:livewire:navigate.window="pageLoading = true"
            x-on:livewire:navigated.window="pageLoading = false; window.applyTheme()"
            x-on:livewire:navigate-cancelled.window="pageLoading = false"
            class="min-h-screen"
        >
            <x-page-loader />
            <div wire:loading.delay.longer class="fixed end-5 top-20 z-[65]">
                <div class="rounded-2xl border border-emerald-200 bg-white/95 px-4 py-3 shadow-xl shadow-slate-900/10 dark:border-emerald-400/20 dark:bg-slate-900/95">
                    <x-loading />
                </div>
            </div>

            <livewire:layout.navigation />

            <x-sidebar />

            <div
                class="min-h-screen transition-all duration-300"
                :class="sidebarCollapsed ? '{{ $dir === 'rtl' ? 'lg:mr-24' : 'lg:ml-24' }}' : '{{ $dir === 'rtl' ? 'lg:mr-72' : 'lg:ml-72' }}'"
            >
                <x-topbar :title="$pageTitle" />

                <main class="px-4 py-5 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-[1500px]">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
