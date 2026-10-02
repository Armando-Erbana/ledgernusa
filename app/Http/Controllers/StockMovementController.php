<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'warehouse'])->latest('date')->latest('id');
        if ($request->filled('product_id')) $query->where('product_id', $request->product_id);
        if ($request->filled('warehouse_id')) $query->where('warehouse_id', $request->warehouse_id);
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('from')) $query->where('date', '>=', $request->from);
        if ($request->filled('to')) $query->where('date', '<=', $request->to);

        $movements = $query->paginate(30)->withQueryString();
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        return view('stock.index', compact('movements', 'products', 'warehouses'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        return view('stock.create', compact('products', 'warehouses'));
    }

    public function store(Request $request, StockService $stock)
    {
        $data = $request->validate([
            'type' => 'required|in:in,out,adjustment',
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'qty' => 'required|numeric|min:0.0001',
            'unit_cost' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $meta = [
            'date' => $data['date'],
            'type' => $data['type'],
            'reference' => $data['reference'] ?? null,
            'description' => $data['description'] ?? null,
            'source_type' => 'manual',
        ];

        try {
            if ($data['type'] === 'in') {
                $stock->stockIn($data['product_id'], $data['warehouse_id'], $data['qty'], $data['unit_cost'] ?? 0, $meta);
            } elseif ($data['type'] === 'out') {
                $stock->stockOut($data['product_id'], $data['warehouse_id'], $data['qty'], $meta);
            } else {
                $stock->adjust($data['product_id'], $data['warehouse_id'], $data['qty'], $data['description'] ?? null);
            }
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('stock.index')->with('success', 'Mutasi stok berhasil dicatat.');
    }

    public function destroy(StockMovement $stock)
    {
        return back()->with('error', 'Mutasi stok tidak bisa dihapus. Buat adjustment baru kalau perlu koreksi.');
    }
}