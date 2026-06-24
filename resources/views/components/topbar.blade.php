@props(['title' => __('common.dashboard')])

<header class="sticky top-0 z-30 border-b border-slate-200/70 bg-white/95 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950">
    <div class="flex min-h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <x-icon-button @click="sidebarOpen = true" label="Menu" class="lg:hidden">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </x-icon-button>
        </div>

        <div class="flex flex-1 items-center justify-end gap-3">
            <form
                method="GET"
                action="{{ route('global.search') }}"
                x-on:submit.prevent="Livewire.navigate($el.action + '?q=' + encodeURIComponent($el.elements.q.value))"
                class="hidden w-full max-w-md items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-slate-500 shadow-sm transition focus-within:border-emerald-400 focus-within:ring-4 focus-within:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 md:flex"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input name="q" value="{{ request('q') }}" type="search" placeholder="{{ __('common.search') }}" class="w-full border-0 bg-transparent p-0 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0 dark:text-slate-100">
            </form>

            <form method="POST" action="{{ route('locale.switch') }}">
                @csrf
                <select name="locale" onchange="this.form.submit()" class="ui-field min-h-10 py-2">
                    <option value="fa" @selected(app()->getLocale() === 'fa')>&#1583;&#1585;&#1740;</option>
                    <option value="ps" @selected(app()->getLocale() === 'ps')>&#1662;&#1690;&#1578;&#1608;</option>
                    <option value="en" @selected(app()->getLocale() === 'en')>English</option>
                </select>
            </form>

            <x-icon-button
                type="button"
                label="Theme"
                @click="window.toggleTheme()"
            >
                <svg class="h-5 w-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.36-6.36-1.42 1.42M7.05 16.95l-1.41 1.41m12.72 0-1.42-1.41M7.05 7.05 5.64 5.64"/><circle cx="12" cy="12" r="4"/></svg>
                <svg class="hidden h-5 w-5 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z"/></svg>
            </x-icon-button>

            @php
                $lowStockCount = \App\Models\Product::with('productBatches')->get()->filter(fn ($product) => $product->current_stock <= $product->minimum_stock)->count();
                $nearExpiryCount = app(\App\Services\ExpiryAlertService::class)->nearExpiry()->count();
                $expiredCount = app(\App\Services\ExpiryAlertService::class)->expired()->count();
                $customerDueCount = \App\Models\Sale::where('due_amount', '>', 0)->count();
                $supplierDueCount = \App\Models\Purchase::where('due_amount', '>', 0)->count();
                $notificationCount = $lowStockCount + $nearExpiryCount + $expiredCount + $customerDueCount + $supplierDueCount;
                $recentActivities = \App\Models\ActivityLog::latest()->limit(3)->get();
            @endphp
            <div x-data="{ open: false }" class="relative">
                <x-icon-button type="button" label="Notifications" class="relative" @click="open = !open">
                    @if ($notificationCount > 0)
                        <span class="absolute -end-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white ring-2 ring-white dark:ring-slate-950">{{ $notificationCount > 9 ? '9+' : $notificationCount }}</span>
                    @endif
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                </x-icon-button>

                <div x-show="open" x-transition @click.outside="open = false" class="absolute end-0 mt-2 w-96 max-w-[calc(100vw-2rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/12 dark:border-slate-700 dark:bg-slate-900">
                    <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-bold text-slate-950 dark:text-white">Notifications</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $notificationCount }} active alerts</p>
                            </div>
                            <x-badge variant="{{ $notificationCount > 0 ? 'danger' : 'success' }}">{{ $notificationCount }}</x-badge>
                        </div>
                    </div>

                    <div class="max-h-[28rem] overflow-y-auto p-2">
                        <a href="{{ route('advanced.page', 'low-stock-products') }}" wire:navigate class="flex items-center justify-between gap-3 rounded-2xl px-3 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('sidebar.low_stock') }}</span>
                            <x-badge variant="{{ $lowStockCount > 0 ? 'warning' : 'success' }}">{{ $lowStockCount }}</x-badge>
                        </a>
                        <a href="{{ route('advanced.page', 'near-expiry-products') }}" wire:navigate class="flex items-center justify-between gap-3 rounded-2xl px-3 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('sidebar.expiring_products') }}</span>
                            <x-badge variant="{{ $nearExpiryCount > 0 ? 'warning' : 'success' }}">{{ $nearExpiryCount }}</x-badge>
                        </a>
                        <a href="{{ route('advanced.page', 'expired-products') }}" wire:navigate class="flex items-center justify-between gap-3 rounded-2xl px-3 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('sidebar.expired_products') }}</span>
                            <x-badge variant="{{ $expiredCount > 0 ? 'danger' : 'success' }}">{{ $expiredCount }}</x-badge>
                        </a>
                        <a href="{{ route('advanced.page', 'customer-due-payments') }}" wire:navigate class="flex items-center justify-between gap-3 rounded-2xl px-3 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('sidebar.customer_due_payments') }}</span>
                            <x-badge variant="{{ $customerDueCount > 0 ? 'info' : 'success' }}">{{ $customerDueCount }}</x-badge>
                        </a>
                        <a href="{{ route('advanced.page', 'supplier-due-payments') }}" wire:navigate class="flex items-center justify-between gap-3 rounded-2xl px-3 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('sidebar.supplier_due_payments') }}</span>
                            <x-badge variant="{{ $supplierDueCount > 0 ? 'info' : 'success' }}">{{ $supplierDueCount }}</x-badge>
                        </a>

                        @if ($recentActivities->isNotEmpty())
                            <div class="mt-2 border-t border-slate-100 px-3 pt-3 dark:border-slate-800">
                                <p class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-400">Recent activity</p>
                                <div class="space-y-2">
                                    @foreach ($recentActivities as $activity)
                                        <div class="rounded-2xl bg-slate-50 px-3 py-2 dark:bg-slate-950">
                                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $activity->module }} / {{ $activity->action }}</p>
                                            <p class="mt-1 line-clamp-2 text-xs text-slate-500 dark:text-slate-400">{{ $activity->description }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800">
                    <span class="grid h-8 w-8 place-items-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300">{{ str(auth()->user()?->name ?? 'U')->substr(0, 1)->upper() }}</span>
                    <span class="hidden sm:block">{{ auth()->user()?->name }}</span>
                </button>
                <div x-show="open" x-transition @click.outside="open = false" class="absolute end-0 mt-2 w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900">
                    <a href="{{ route('profile') }}" wire:navigate class="block px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-white/5">{{ __('Profile') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-3 text-start text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-400/10">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
