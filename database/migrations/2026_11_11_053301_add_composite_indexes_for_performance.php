<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Index untuk Jurnal
        Schema::table('journals', function (Blueprint $t) {
            $t->index(['company_id', 'branch_id', 'date', 'status'], 'idx_journals_perf');
        });

        // Index untuk Detail Jurnal
        Schema::table('journal_entries', function (Blueprint $t) {
            $t->index(['journal_id', 'account_id'], 'idx_entries_perf');
            $t->index('account_id', 'idx_account_perf');
        });

        // Index untuk Penjualan & Kas
        Schema::table('sales', function (Blueprint $t) {
            $t->index(['company_id', 'branch_id', 'date'], 'idx_sales_perf');
        });
        Schema::table('cash_transactions', function (Blueprint $t) {
            $t->index(['company_id', 'branch_id', 'date'], 'idx_cash_perf');
        });
    }

    public function down(): void
    {
        // Drop index jika rollback
    }
};