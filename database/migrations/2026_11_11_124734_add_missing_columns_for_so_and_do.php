<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_orders', 'branch_id')) {
                $table->foreignId('branch_id')->nullable();
            }
        });

        Schema::table('sales_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_order_items', 'delivered_qty')) {
                $table->decimal('delivered_qty', 18, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        // biarkan kosong
    }
};