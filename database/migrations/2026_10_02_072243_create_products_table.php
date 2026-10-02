<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();

            $table->string('code', 50);
            $table->string('name');
            $table->string('unit', 20)->default('pcs');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // Akun terkait
            $table->foreignId('inventory_account_id')->nullable()->constrained('accounts');  // Persediaan
            $table->foreignId('sales_account_id')->nullable()->constrained('accounts');      // Pendapatan
            $table->foreignId('cogs_account_id')->nullable()->constrained('accounts');       // HPP

            // Harga
            $table->decimal('cost_price', 18, 2)->default(0);   // harga pokok rata-rata
            $table->decimal('sell_price', 18, 2)->default(0);   // harga jual default

            // Stok
            $table->decimal('stock', 18, 4)->default(0);        // total stok semua gudang
            $table->decimal('min_stock', 18, 4)->default(0);    // minimum stok (alert)
            $table->boolean('track_stock')->default(true);      // apakah track stok?
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->unique(['company_id', 'code']);
            $table->index(['company_id', 'name']);
        });
    }
    public function down(): void { Schema::dropIfExists('products'); }
};