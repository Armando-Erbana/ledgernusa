<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('sale_id')->constrained('products')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->after('product_id')->constrained('warehouses')->nullOnDelete();
            $table->decimal('cost_price', 18, 4)->default(0)->after('subtotal');
            $table->decimal('total_cost', 18, 2)->default(0)->after('cost_price');
        });
    }
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropConstrainedForeignId('warehouse_id');
            $table->dropColumn(['cost_price', 'total_cost']);
        });
    }
};