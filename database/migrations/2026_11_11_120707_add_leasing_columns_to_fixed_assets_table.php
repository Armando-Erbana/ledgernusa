<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            // Menambahkan kolom untuk mencatat pembelian kredit/leasing
            $table->decimal('down_payment', 18, 2)->default(0);
            $table->decimal('loan_amount', 18, 2)->default(0);
            $table->integer('tenor_months')->default(0);
            $table->decimal('interest_rate', 5, 2)->default(0); 
            $table->foreignId('liability_account_id')->nullable()->constrained('accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->dropForeign(['liability_account_id']);
            $table->dropColumn(['down_payment', 'loan_amount', 'tenor_months', 'interest_rate', 'liability_account_id']);
        });
    }
};