<?php
namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Tambah stok (pembelian, retur penjualan, dll).
     * Otomatis hitung rata-rata bergerak (weighted average).
     */
    public function stockIn(int $productId, int $warehouseId, float $qty, float $unitCost, array $meta = []): StockMovement
    {
        return DB::transaction(function () use ($productId, $warehouseId, $qty, $unitCost, $meta) {
            $product = Product::findOrFail($productId);

            $stockBefore = (float) $product->stock;
            $stockAfter = $stockBefore + $qty;

            // Weighted Average Cost
            $oldValue = $stockBefore * (float) $product->cost_price;
            $newValue = $qty * $unitCost;
            $newAvgCost = $stockAfter > 0 ? round(($oldValue + $newValue) / $stockAfter, 4) : $unitCost;

            $product->update([
                'stock' => $stockAfter,
                'cost_price' => $newAvgCost,
            ]);

            return StockMovement::create([
                'company_id' => $product->company_id,
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'date' => $meta['date'] ?? now(),
                'type' => $meta['type'] ?? 'in',
                'reference' => $meta['reference'] ?? null,
                'description' => $meta['description'] ?? null,
                'qty' => $qty,
                'unit_cost' => $unitCost,
                'total_cost' => $qty * $unitCost,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'source_type' => $meta['source_type'] ?? null,
                'source_id' => $meta['source_id'] ?? null,
                'created_by' => auth()->id(),
            ]);
        });
    }

    /**
     * Kurangi stok (penjualan, pemakaian, dll).
     * Otomatis pakai cost_price saat ini (weighted average).
     */
    public function stockOut(int $productId, int $warehouseId, float $qty, array $meta = []): StockMovement
    {
        return DB::transaction(function () use ($productId, $warehouseId, $qty, $meta) {
            $product = Product::findOrFail($productId);

            if ($product->track_stock && $product->stock < $qty) {
                throw new \Exception("Stok {$product->name} tidak cukup. Tersedia: {$product->stock}, diminta: {$qty}");
            }

            $stockBefore = (float) $product->stock;
            $stockAfter = $stockBefore - $qty;
            $unitCost = (float) $product->cost_price;

            $product->update(['stock' => $stockAfter]);

            return StockMovement::create([
                'company_id' => $product->company_id,
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'date' => $meta['date'] ?? now(),
                'type' => $meta['type'] ?? 'out',
                'reference' => $meta['reference'] ?? null,
                'description' => $meta['description'] ?? null,
                'qty' => $qty,
                'unit_cost' => $unitCost,
                'total_cost' => $qty * $unitCost,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'source_type' => $meta['source_type'] ?? null,
                'source_id' => $meta['source_id'] ?? null,
                'created_by' => auth()->id(),
            ]);
        });
    }

    /**
     * Adjustment stok (stock opname).
     */
    public function adjust(int $productId, int $warehouseId, float $newQty, string $reason = null): StockMovement
    {
        return DB::transaction(function () use ($productId, $warehouseId, $newQty, $reason) {
            $product = Product::findOrFail($productId);
            $stockBefore = (float) $product->stock;
            $diff = $newQty - $stockBefore;

            if ($diff == 0) throw new \Exception('Tidak ada perubahan stok.');

            $product->update(['stock' => $newQty]);

            return StockMovement::create([
                'company_id' => $product->company_id,
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'date' => now(),
                'type' => 'adjustment',
                'description' => $reason ?? 'Stock opname',
                'qty' => abs($diff),
                'unit_cost' => (float) $product->cost_price,
                'total_cost' => abs($diff) * (float) $product->cost_price,
                'stock_before' => $stockBefore,
                'stock_after' => $newQty,
                'created_by' => auth()->id(),
            ]);
        });
    }
}