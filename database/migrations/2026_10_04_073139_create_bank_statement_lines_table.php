<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_statement_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_statement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('description');
            $table->string('reference')->nullable();
            $table->decimal('debit', 18, 2)->default(0);   // uang masuk
            $table->decimal('credit', 18, 2)->default(0);  // uang keluar
            $table->decimal('balance', 18, 2)->nullable();
            $table->enum('match_status', ['unmatched', 'matched', 'excluded'])->default('unmatched');

            // Referensi ke transaksi sistem yang cocok
            $table->string('matched_type')->nullable();     // cash_transaction, sale_payment, purchase_payment
            $table->unsignedBigInteger('matched_id')->nullable();
            $table->timestamp('matched_at')->nullable();
            $table->foreignId('matched_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->index(['bank_statement_id', 'match_status']);
            $table->index(['matched_type', 'matched_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('bank_statement_lines'); }
};