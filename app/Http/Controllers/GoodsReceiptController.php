<?php
namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsReceiptController extends Controller
{
    public function index(Request $request)
    {
        $query = GoodsReceipt::with('supplier', 'purchaseOrder')->latest('date')->latest('id');
        if ($request->filled('status')) $query->where('status', $request->status);
        $grs = $query->paginate(20)->withQueryString();
        return view('goods-receipts.index', compact('grs'));
    }

    public function create(Request $request)
    {
        $poId = $request->get('po');
        $po = $poId ? PurchaseOrder::with('items.product', 'supplier')->findOrFail($poId) : null;

        $availablePOs = PurchaseOrder::whereIn('status', ['confirmed', 'partial_received'])
            ->with('supplier')
            ->orderBy('date', 'desc')
            ->get();

        $nextNumber = 'GR-' . date('Ym') . '-' . str_pad(GoodsReceipt::where('company_id', session('company_id'))->count() + 1, 4, '0', STR_PAD_LEFT);

        return view('goods-receipts.create', compact('po', 'availablePOs', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'gr_number' => 'required|string|max:50|unique:goods_receipts,gr_number',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'date' => 'required|date',
            'received_by_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.warehouse_id' => 'nullable|exists:warehouses,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($data) {
            $po = !empty($data['purchase_order_id']) ? PurchaseOrder::find($data['purchase_order_id']) : null;

            $gr = GoodsReceipt::create([
                'company_id' => session('company_id'),
                'purchase_order_id' => $data['purchase_order_id'] ?? null,
                'gr_number' => $data['gr_number'],
                'supplier_id' => $po?->supplier_id,
                'date' => $data['date'],
                'received_by_name' => $data['received_by_name'] ?? null,
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $gr->items()->create([
                    'purchase_order_item_id' => $item['purchase_order_item_id'] ?? null,
                    'product_id' => $item['product_id'] ?? null,
                    'warehouse_id' => $item['warehouse_id'] ?? null,
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'unit_cost' => $item['unit_cost'] ?? 0,
                ]);
            }
        });

        return redirect()->route('goods-receipts.index')->with('success', 'GR berhasil dibuat.');
    }

    public function show(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load(['supplier', 'purchaseOrder', 'items.product', 'items.warehouse']);
        return view('goods-receipts.show', ['gr' => $goodsReceipt]);
    }

    public function destroy(GoodsReceipt $goodsReceipt)
    {
        if ($goodsReceipt->status !== 'draft') return back()->with('error', 'Hanya GR draft yang bisa dihapus.');
        $goodsReceipt->items()->delete();
        $goodsReceipt->delete();
        return redirect()->route('goods-receipts.index')->with('success', 'GR dihapus.');
    }

    public function receive(GoodsReceipt $goodsReceipt, StockService $stockService)
    {
        if ($goodsReceipt->status === 'received') return back()->with('error', 'GR sudah diterima.');

        DB::transaction(function () use ($goodsReceipt, $stockService) {
            foreach ($goodsReceipt->items as $item) {
                if ($item->product_id && $item->warehouse_id) {
                    $product = Product::find($item->product_id);
                    if ($product && $product->track_stock) {
                        $stockService->stockIn($product->id, $item->warehouse_id, $item->qty, $item->unit_cost, [
                            'date' => $goodsReceipt->date,
                            'type' => 'in',
                            'reference' => $goodsReceipt->gr_number,
                            'description' => 'Penerimaan ' . $goodsReceipt->gr_number,
                            'source_type' => 'goods_receipt',
                            'source_id' => $goodsReceipt->id,
                        ]);
                    }
                }

                if ($item->purchase_order_item_id) {
                    $poItem = \App\Models\PurchaseOrderItem::find($item->purchase_order_item_id);
                    if ($poItem) $poItem->increment('received_qty', $item->qty);
                }
            }

            $goodsReceipt->update([
                'status' => 'received',
                'received_by' => auth()->id(),
                'received_at' => now(),
            ]);

            if ($goodsReceipt->purchaseOrder) {
                $po = $goodsReceipt->purchaseOrder->fresh();
                $po->update(['status' => $po->isFullyReceived() ? 'received' : 'partial_received']);
            }
        });

        return back()->with('success', 'GR diterima, stok bertambah.');
    }

    public function createBill(GoodsReceipt $goodsReceipt)
    {
        if ($goodsReceipt->status !== 'received') {
            return back()->with('error', 'GR harus diterima dulu sebelum dibuat bill.');
        }
        if (!$goodsReceipt->purchaseOrder) {
            return back()->with('error', 'GR tidak terkait dengan PO.');
        }
        return redirect()->route('purchases.create', ['from_gr' => $goodsReceipt->id]);
    }
}