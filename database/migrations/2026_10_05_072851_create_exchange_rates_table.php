<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('from_currency', 3);
            $table->string('to_currency', 3);
            $table->decimal('rate', 18, 8);
            $table->date('effective_date');
            $table->string('source', 50)->nullable();
            $table->timestamps();

            $table->index(
                ['company_id', 'from_currency', 'to_currency', 'effective_date'],
                'idx_rate_lookup'
            );
            $table->unique(
                ['company_id', 'from_currency', 'to_currency', 'effective_date'],
                'uq_rate'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};