<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code', 30)->nullable();
            $table->string('name');
            $table->string('npwp')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->decimal('credit_limit', 18, 2)->default(0);
            $table->integer('payment_term_days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['company_id', 'name']);
        });
    }
    public function down(): void { Schema::dropIfExists('customers'); }
};