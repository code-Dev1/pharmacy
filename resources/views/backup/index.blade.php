<x-admin-layout :title="__('sidebar.backup_settings')">
    <div class="space-y-6">
        <x-card :title="__('settings.backup_database')" :description="__('settings.backup_download_description')">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('settings.current_database') }}</p>
                    <p class="mt-1 break-all text-sm text-slate-500 dark:text-slate-400">{{ $databasePath ?? '-' }}</p>
                </div>
                <a href="{{ route('backup.download') }}">
                    <x-button type="button">{{ __('settings.download_backup') }}</x-button>
                </a>
            </div>

            @unless ($isSqlite)
                <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-200">
                    {{ __('settings.backup_sqlite_only') }}
                </div>
            @endunless
        </x-card>

        <x-card :title="__('settings.restore_database')" :description="__('settings.restore_description')">
            <form method="POST" action="{{ route('backup.restore') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <label class="block">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ __('settings.database_file') }}</span>
                    <input name="database" type="file" accept=".sqlite,.db,.database" class="ui-field mt-2 file:me-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 dark:file:bg-emerald-400/10 dark:file:text-emerald-200">
                    @error('database')<span class="mt-2 block text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</span>@enderror
                </label>

                <label class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 dark:border-rose-400/20 dark:bg-rose-400/10">
                    <input name="confirm_restore" value="1" type="checkbox" class="mt-1 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                    <span class="text-sm font-semibold text-rose-700 dark:text-rose-200">{{ __('settings.restore_confirm') }}</span>
                </label>
                @error('confirm_restore')<span class="block text-xs font-semibold text-rose-600 dark:text-rose-300">{{ $message }}</span>@enderror

                <x-button type="submit" variant="danger">{{ __('settings.upload_restore') }}</x-button>
            </form>
        </x-card>
    </div>
</x-admin-layout>
