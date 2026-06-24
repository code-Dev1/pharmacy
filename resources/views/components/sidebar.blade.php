@php
    $isRtl = \App\Support\Locale::isRtl();
    $item = fn ($key, $href, $active = false) => ['key' => $key, 'href' => $href, 'active' => $active];
    $menus = [
        ['label' => 'sidebar.products', 'icon' => 'box', 'active' => request()->is('pharmacy/categories*') || request()->is('pharmacy/products*') || request()->is('pharmacy/batches*'), 'items' => [
            $item('sidebar.categories', route('pharmacy.index', 'categories'), request()->is('pharmacy/categories*')),
            $item('sidebar.products', route('pharmacy.index', 'products'), request()->is('pharmacy/products*')),
            $item('sidebar.product_batches', route('pharmacy.index', 'batches'), request()->is('pharmacy/batches*')),
            $item('sidebar.low_stock', route('advanced.page', 'low-stock-products'), request()->is('advanced/low-stock-products*')),
            $item('sidebar.expiring_products', route('advanced.page', 'near-expiry-products'), request()->is('advanced/near-expiry-products*')),
        ]],
        ['label' => 'sidebar.purchases', 'icon' => 'cart', 'active' => request()->is('pharmacy/purchases*') || request()->is('purchases/create') || request()->is('pharmacy/purchase-payments*'), 'items' => [
            $item('sidebar.purchase_list', route('pharmacy.index', 'purchases'), request()->is('pharmacy/purchases*')),
            $item('sidebar.new_purchase', route('purchases.create'), request()->is('purchases/create')),
            $item('sidebar.purchase_payments', route('pharmacy.index', 'purchase-payments'), request()->is('pharmacy/purchase-payments*')),
            $item('sidebar.supplier_due_payments', route('advanced.page', 'supplier-due-payments'), request()->is('advanced/supplier-due-payments*')),
        ]],
        ['label' => 'sidebar.sales', 'icon' => 'sales', 'active' => request()->is('pharmacy/sales*') || request()->is('sales/create') || request()->is('pharmacy/sale-payments*'), 'items' => [
            $item('sidebar.sales_list', route('pharmacy.index', 'sales'), request()->is('pharmacy/sales*')),
            $item('sidebar.pos', route('sales.create'), request()->is('sales/create')),
            $item('sidebar.sale_payments', route('pharmacy.index', 'sale-payments'), request()->is('pharmacy/sale-payments*')),
            $item('sidebar.customer_due_payments', route('advanced.page', 'customer-due-payments'), request()->is('advanced/customer-due-payments*')),
        ]],
        ['label' => 'sidebar.stock', 'icon' => 'stock', 'active' => request()->is('pharmacy/stock-*') || request()->is('stock-adjustments/create'), 'items' => [
            $item('sidebar.stock_movements', route('pharmacy.index', 'stock-movements'), request()->is('pharmacy/stock-movements*')),
            $item('sidebar.stock_adjustments', route('pharmacy.index', 'stock-adjustments'), request()->is('pharmacy/stock-adjustments*')),
            $item('sidebar.damaged_products', route('pharmacy.index', 'stock-movements')),
            $item('sidebar.expired_products', route('advanced.page', 'expired-products'), request()->is('advanced/expired-products*')),
        ]],
        ['label' => 'sidebar.returns', 'icon' => 'returns', 'active' => request()->is('pharmacy/*returns*'), 'items' => [
            $item('sidebar.return_list', route('pharmacy.index', 'returns'), request()->is('pharmacy/returns*')),
            $item('sidebar.sale_return', route('pharmacy.create', 'sale-returns'), request()->is('pharmacy/sale-returns*')),
            $item('sidebar.purchase_return', route('pharmacy.create', 'purchase-returns'), request()->is('pharmacy/purchase-returns*')),
        ]],
        ['label' => 'sidebar.people', 'icon' => 'users', 'active' => request()->is('pharmacy/customers*') || request()->is('pharmacy/suppliers*'), 'items' => [
            $item('sidebar.customers', route('pharmacy.index', 'customers'), request()->is('pharmacy/customers*')),
            $item('sidebar.suppliers', route('pharmacy.index', 'suppliers'), request()->is('pharmacy/suppliers*')),
        ]],
        ['label' => 'sidebar.expenses', 'icon' => 'expenses', 'active' => request()->is('pharmacy/expense*'), 'items' => [
            $item('sidebar.expense_categories', route('pharmacy.index', 'expense-categories'), request()->is('pharmacy/expense-categories*')),
            $item('sidebar.expenses_list', route('pharmacy.index', 'expenses'), request()->is('pharmacy/expenses')),
            $item('sidebar.new_expense', route('pharmacy.create', 'expenses')),
        ]],
        ['label' => 'sidebar.reports', 'icon' => 'reports', 'active' => request()->is('advanced/*report*') || request()->is('pharmacy/reports*'), 'items' => [
            $item('sidebar.sales_report', route('advanced.page', 'sales-report'), request()->is('advanced/sales-report*')),
            $item('sidebar.purchase_report', route('advanced.page', 'purchase-report'), request()->is('advanced/purchase-report*')),
            $item('sidebar.stock_report', route('advanced.page', 'stock-report'), request()->is('advanced/stock-report*')),
            $item('sidebar.expiry_report', route('advanced.page', 'expiry-report'), request()->is('advanced/expiry-report*')),
            $item('sidebar.profit_loss_report', route('advanced.page', 'profit-loss-report'), request()->is('advanced/profit-loss-report*')),
            $item('sidebar.customer_due_report', route('advanced.page', 'customer-due-report'), request()->is('advanced/customer-due-report*')),
            $item('sidebar.supplier_due_report', route('advanced.page', 'supplier-due-report'), request()->is('advanced/supplier-due-report*')),
            $item('sidebar.expense_report', route('advanced.page', 'expense-report'), request()->is('advanced/expense-report*')),
        ]],
        ['label' => 'sidebar.settings', 'icon' => 'settings', 'active' => request()->is('pharmacy/settings*'), 'items' => [
            $item('sidebar.pharmacy_settings', route('pharmacy.index', 'settings'), request()->is('pharmacy/settings*')),
            // $item('sidebar.users', route('pharmacy.index', 'settings')),
            // $item('sidebar.roles_permissions', route('pharmacy.index', 'settings')),
            // $item('sidebar.language_settings', route('pharmacy.index', 'settings')),
            $item('sidebar.backup_settings', route('pharmacy.index', 'settings')),
        ]],
        ['label' => 'sidebar.system', 'icon' => 'settings', 'active' => request()->is('pharmacy/activity-logs*'), 'items' => [$item('sidebar.activity_logs', route('pharmacy.index', 'activity-logs'), request()->is('pharmacy/activity-logs*'))]],
    ];
@endphp

<div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-sm lg:hidden"></div>

<aside
    class="fixed inset-y-0 z-50 flex flex-col border-slate-200/70 bg-white shadow-2xl shadow-slate-900/10 transition-all duration-300 dark:border-slate-800 dark:bg-slate-950 {{ $isRtl ? 'right-0 border-l' : 'left-0 border-r' }}"
    :class="[
        sidebarCollapsed ? 'w-24' : 'w-72',
        sidebarOpen ? 'translate-x-0' : '{{ $isRtl ? 'translate-x-full' : '-translate-x-full' }} lg:translate-x-0'
    ]"
>
    <div class="flex items-center h-20 gap-3 px-5 border-b border-slate-200/70 dark:border-slate-800">
        <div class="grid text-white shadow-lg h-11 w-11 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-emerald-500 via-teal-500 to-sky-500 shadow-emerald-500/25">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 21h8"/><path d="M12 17V3"/><path d="M7 8h10"/><path d="M5 13h14"/></svg>
        </div>
        <div x-show="!sidebarCollapsed" x-transition.opacity>
            <p class="text-sm font-semibold text-slate-950 dark:text-white">{{ __('common.app_name') }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-300">{{ __('sidebar.overview') }}</p>
        </div>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="dashboard">{{ __('sidebar.dashboard') }}</x-sidebar-link>

        @foreach ($menus as $menu)
            <x-sidebar-dropdown :label="__($menu['label'])" :icon="$menu['icon']" :active="$menu['active']">
                @foreach ($menu['items'] as $child)
                    <x-sidebar-link :href="$child['href']" :active="$child['active']">{{ __($child['key']) }}</x-sidebar-link>
                @endforeach
            </x-sidebar-dropdown>
        @endforeach
    </nav>

    <div class="p-3 border-t border-slate-200/70 dark:border-slate-800">
        <button @click="sidebarCollapsed = !sidebarCollapsed" class="flex items-center justify-center w-full px-3 py-2 transition border rounded-xl border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
            <svg class="w-5 h-5 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L9.06 10l3.71 3.71a.75.75 0 1 1-1.06 1.06l-4.24-4.24a.75.75 0 0 1 0-1.06l4.24-4.24a.75.75 0 0 1 1.08 0Z" clip-rule="evenodd"/></svg>
        </button>
    </div>
</aside>
