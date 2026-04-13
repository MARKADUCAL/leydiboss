<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('wallet_transactions', 'customer_id')) {
                $table->foreignId('customer_id')
                    ->nullable()
                    ->constrained('customers')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('wallet_transactions', 'admin_id')) {
                $table->foreignId('admin_id')
                    ->nullable()
                    ->constrained('admins')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('wallet_transactions', 'description')) {
                $table->string('description');
            }

            if (!Schema::hasColumn('wallet_transactions', 'type')) {
                $table->enum('type', ['debit', 'credit']);
            }

            if (!Schema::hasColumn('wallet_transactions', 'value')) {
                $table->decimal('value', 12, 2);
            }
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Drop foreign keys first before dropping columns
            if (Schema::hasColumn('wallet_transactions', 'admin_id')) {
                $table->dropForeign(['admin_id']);
            }

            if (Schema::hasColumn('wallet_transactions', 'customer_id')) {
                $table->dropForeign(['customer_id']);
            }

            // In down(), we remove columns only if they exist.
            if (Schema::hasColumn('wallet_transactions', 'value')) {
                $table->dropColumn('value');
            }

            if (Schema::hasColumn('wallet_transactions', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('wallet_transactions', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('wallet_transactions', 'admin_id')) {
                $table->dropColumn('admin_id');
            }

            if (Schema::hasColumn('wallet_transactions', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
        });
    }
};

