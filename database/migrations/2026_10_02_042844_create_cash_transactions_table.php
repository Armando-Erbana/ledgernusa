<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('journal_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['in', 'out']);                    // kas masuk / keluar
            $table->date('date');
            $table->string('reference')->nullable();                 // no bukti
            $table->foreignId('cash_account_id')->constrained('accounts'); // akun kas/bank
            $table->foreignId('counter_account_id')->constrained('accounts'); // akun lawan
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 18, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'posted'])->default('posted');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'date']);
            $table->index(['company_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};