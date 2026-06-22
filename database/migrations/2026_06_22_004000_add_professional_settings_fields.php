<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'timezone')) {
                $table->string('timezone')->default('Asia/Kabul')->after('expiry_alert_days');
            }
            if (! Schema::hasColumn('settings', 'date_format')) {
                $table->string('date_format')->default('Y-m-d')->after('timezone');
            }
            if (! Schema::hasColumn('settings', 'default_language')) {
                $table->string('default_language')->default('fa')->after('date_format');
            }
            if (! Schema::hasColumn('settings', 'default_theme')) {
                $table->string('default_theme')->default('light')->after('default_language');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            foreach (['default_theme', 'default_language', 'date_format', 'timezone'] as $column) {
                if (Schema::hasColumn('settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
