<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PDO;
use Throwable;

class DatabaseBackupController extends Controller
{
    public function index()
    {
        return view('backup.index', [
            'databasePath' => $this->databasePath(),
            'isSqlite' => $this->isSqlite(),
        ]);
    }

    public function download()
    {
        if (! $this->isSqlite()) {
            return back()->with('toast', __('settings.backup_sqlite_only'))->with('toast_variant', 'warning');
        }

        $path = $this->databasePath();
        abort_unless($path && File::exists($path), 404);

        $filename = 'database-backup-' . now()->format('Y-m-d-His') . '.sqlite';

        return response()->download($path, $filename);
    }

    public function restore(Request $request)
    {
        if (! $this->isSqlite()) {
            return back()->with('toast', __('settings.backup_sqlite_only'))->with('toast_variant', 'warning');
        }

        $request->validate([
            'database' => ['required', 'file', 'max:51200'],
            'confirm_restore' => ['accepted'],
        ]);

        $upload = $request->file('database');
        $extension = Str::lower($upload->getClientOriginalExtension());

        if (! in_array($extension, ['sqlite', 'db', 'database'], true)) {
            return back()->with('toast', __('settings.backup_invalid_file'))->with('toast_variant', 'danger');
        }

        $currentPath = $this->databasePath();
        abort_unless($currentPath && File::exists($currentPath), 404);

        $temporaryPath = $upload->getRealPath();
        if (! $temporaryPath || ! $this->isValidSqliteDatabase($temporaryPath)) {
            return back()->with('toast', __('settings.backup_invalid_file'))->with('toast_variant', 'danger');
        }

        $backupPath = storage_path('app/backups/database-before-restore-' . now()->format('Y-m-d-His') . '.sqlite');
        File::ensureDirectoryExists(dirname($backupPath));
        File::copy($currentPath, $backupPath);

        DB::disconnect();
        File::copy($temporaryPath, $currentPath);

        return back()->with('toast', __('settings.restore_success'));
    }

    private function isSqlite(): bool
    {
        return config('database.default') === 'sqlite';
    }

    private function databasePath(): ?string
    {
        if (! $this->isSqlite()) {
            return null;
        }

        $path = config('database.connections.sqlite.database');

        return $path ? (string) $path : null;
    }

    private function isValidSqliteDatabase(string $path): bool
    {
        try {
            $pdo = new PDO('sqlite:' . $path);
            $pdo->query("select name from sqlite_master where type = 'table' limit 1");

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
