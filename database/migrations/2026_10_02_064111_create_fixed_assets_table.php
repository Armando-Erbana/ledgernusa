<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('journal_id')->nullable()->constrained()->nullOnDelete();

            $table->string('code', 30)->nullable();
            $table->string('name');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();

            $table->foreignId('asset_account_id')->constrained('accounts');
            $table->foreignId('depreciation_account_id')->constrained('accounts');
            $table->foreignId('expense_account_id')->constrained('accounts');

            $table->date('purchase_date');
            $table->decimal('purchase_cost', 18, 2);
            $table->decimal('residual_value', 18, 2)->default(0);
            $table->integer('useful_life_months')->default(60);
            $table->enum('depreciation_method', ['straight_line', 'double_declining', 'sum_of_years'])->default('straight_line');

            $table->decimal('accumulated_depreciation', 18, 2)->default(0);
            $table->decimal('book_value', 18, 2)->default(0);
            $table->date('last_depreciation_date')->nullable();
            $table->enum('status', ['active', 'disposed', 'fully_depreciated'])->default('active');
            $table->date('disposed_at')->nullable();
            $table->decimal('disposal_value', 18, 2)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_assets');
    }
};