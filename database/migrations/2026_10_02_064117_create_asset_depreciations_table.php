<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_depreciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fixed_asset_id')->constrained('fixed_assets')->cascadeOnDelete();
            $table->foreignId('journal_id')->nullable()->constrained()->nullOnDelete();

            $table->date('period_date');
            $table->decimal('amount', 18, 2);
            $table->decimal('accumulated_after', 18, 2);
            $table->decimal('book_value_after', 18, 2);
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['fixed_asset_id', 'period_date']);
            $table->index(['company_id', 'period_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_depreciations');
    }
};