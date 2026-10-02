<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();

            $table->date('date');
            $table->enum('type', ['in', 'out', 'adjustment', 'transfer_in', 'transfer_out']);
            $table->string('reference')->nullable();
            $table->text('description')->nullable();

            $table->decimal('qty', 18, 4);                    // selalu positif
            $table->decimal('unit_cost', 18, 4)->default(0);  // harga pokok per unit
            $table->decimal('total_cost', 18, 2)->default(0);
            $table->decimal('stock_before', 18, 4)->default(0);
            $table->decimal('stock_after', 18, 4)->default(0);

            // Referensi ke transaksi sumber
            $table->string('source_type')->nullable();        // 'sale','purchase','adjustment'
            $table->unsignedBigInteger('source_id')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'product_id', 'date']);
            $table->index(['company_id', 'warehouse_id']);
            $table->index(['source_type', 'source_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('stock_movements'); }
};