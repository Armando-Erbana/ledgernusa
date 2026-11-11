<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->safeIndex('journals', ['company_id', 'branch_id', 'date', 'status'], 'journals_co_br_date_st_idx');
        $this->safeIndex('journal_entries', ['journal_id', 'account_id'], 'je_journal_acc_idx');
        $this->safeIndex('journal_entries', ['account_id'], 'je_account_idx');
        $this->safeIndex('sales', ['company_id', 'branch_id', 'date'], 'sales_co_br_date_idx');
        $this->safeIndex('cash_transactions', ['company_id', 'branch_id', 'date'], 'cash_co_br_date_idx');
    }

    public function down(): void
    {
        Schema::table('journals', fn (Blueprint $t) => $t->dropIndex('journals_co_br_date_st_idx'));
        Schema::table('journal_entries', function (Blueprint $t) {
            $t->dropIndex('je_journal_acc_idx');
            $t->dropIndex('je_account_idx');
        });
        Schema::table('sales', fn (Blueprint $t) => $t->dropIndex('sales_co_br_date_idx'));
        Schema::table('cash_transactions', fn (Blueprint $t) => $t->dropIndex('cash_co_br_date_idx'));
    }

    private function safeIndex(string $table, array $columns, string $name): void
    {
        if (! Schema::hasTable($table)) return;

        try {
            Schema::table($table, fn (Blueprint $t) => $t->index($columns, $name));
        } catch (\Throwable $e) {
            // Index sudah ada — abaikan
        }
    }
};