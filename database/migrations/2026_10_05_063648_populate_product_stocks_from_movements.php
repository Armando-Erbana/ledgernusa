<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Agregasi dari stock_movements yang sudah ada
        $rows = DB::table('stock_movements')
            ->select('company_id', 'product_id', 'warehouse_id',
                DB::raw("SUM(CASE WHEN type IN ('in','transfer_in') THEN qty WHEN type IN ('out','transfer_out') THEN -qty ELSE 0 END) as stock"))
            ->groupBy('company_id', 'product_id', 'warehouse_id')
            ->get();

        foreach ($rows as $row) {
            DB::table('product_stocks')->insert([
                'company_id' => $row->company_id,
                'product_id' => $row->product_id,
                'warehouse_id' => $row->warehouse_id,
                'stock' => $row->stock,
                'min_stock' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    public function down(): void
    {
        DB::table('product_stocks')->truncate();
    }
};