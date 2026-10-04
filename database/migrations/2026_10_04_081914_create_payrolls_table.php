<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('journal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payroll_number')->unique();
            $table->integer('period_month');
            $table->integer('period_year');
            $table->date('payment_date');
            $table->decimal('total_gross', 18, 2)->default(0);
            $table->decimal('total_deduction', 18, 2)->default(0);
            $table->decimal('total_pph21', 18, 2)->default(0);
            $table->decimal('total_bpjs', 18, 2)->default(0);
            $table->decimal('total_net', 18, 2)->default(0);
            $table->enum('status', ['draft', 'posted', 'paid'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['company_id', 'period_month', 'period_year']);
        });
    }
    public function down(): void { Schema::dropIfExists('payrolls'); }
};