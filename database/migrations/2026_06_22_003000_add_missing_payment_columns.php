<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_payments', 'payment_method')) {
                $table->string('payment_method')->default('cash')->after('payment_date');
            }
            if (! Schema::hasColumn('purchase_payments', 'reference_no')) {
                $table->string('reference_no')->nullable()->after('payment_method');
            }
            if (Schema::hasColumn('purchase_payments', 'note') && ! Schema::hasColumn('purchase_payments', 'notes')) {
                $table->renameColumn('note', 'notes');
            } elseif (! Schema::hasColumn('purchase_payments', 'notes')) {
                $table->text('notes')->nullable()->after('reference_no');
            }
            if (! Schema::hasColumn('purchase_payments', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('sale_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('sale_payments', 'payment_method')) {
                $table->string('payment_method')->default('cash')->after('payment_date');
            }
            if (! Schema::hasColumn('sale_payments', 'reference_no')) {
                $table->string('reference_no')->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sale_payments', function (Blueprint $table) {
            if (Schema::hasColumn('sale_payments', 'reference_no')) {
                $table->dropColumn('reference_no');
            }
            if (Schema::hasColumn('sale_payments', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });

        Schema::table('purchase_payments', function (Blueprint $table) {
            foreach (['created_by', 'notes', 'reference_no', 'payment_method'] as $column) {
                if (Schema::hasColumn('purchase_payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
