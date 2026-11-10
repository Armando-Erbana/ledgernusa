<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete(); // Link ke Akun COA
            $table->integer('period_month');
            $table->integer('period_year');
            $table->decimal('amount', 18, 2); // Nominal Budget
            $table->timestamps();

            // Mencegah input ganda untuk akun, bulan, dan tahun yang sama di cabang yang sama
            $table->unique(['company_id', 'branch_id', 'account_id', 'period_month', 'period_year'], 'budget_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};