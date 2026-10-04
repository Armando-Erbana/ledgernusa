<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('employee_number', 50);
            $table->string('name');
            $table->string('npwp')->nullable();
            $table->string('ktp')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->date('join_date')->nullable();
            $table->date('resign_date')->nullable();
            $table->enum('employment_type', ['permanent', 'contract', 'freelance'])->default('permanent');
            $table->enum('ptkp_status', ['TK/0', 'TK/1', 'TK/2', 'TK/3', 'K/0', 'K/1', 'K/2', 'K/3'])->default('TK/0');
            $table->decimal('basic_salary', 18, 2)->default(0);
            $table->decimal('fixed_allowance', 18, 2)->default(0);
            $table->decimal('bank_account', 18, 2)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['company_id', 'employee_number']);
            $table->index(['company_id', 'name']);
        });
    }
    public function down(): void { Schema::dropIfExists('employees'); }
};