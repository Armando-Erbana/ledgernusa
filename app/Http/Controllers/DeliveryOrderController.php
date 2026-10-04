<?php
namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryOrder::with('customer', 'salesOrder')->latest('date')->latest('id');
        if ($request->filled('status')) $query->where('status', $request->status);
        $dos = $query->paginate(20)->withQueryString();
        return view('delivery-orders.index', compact('dos'));
    }

    public function create(Request $request)
    {
        $soId = $request->get('so');
        $so = $soId ? SalesOrder::with('items.product', 'customer')->findOrFail($soId) : null;

        // SO yang belum selesai dikirim
        $availableSOs = SalesOrder::whereIn('status', ['confirmed', 'partial_delivered'])
            ->with('customer')
            ->orderBy('date', 'desc')
            ->get();

        $nextNumber = 'DO-' . date('Ym') . '-' . str_pad(DeliveryOrder::where('company_id', session('company_id'))->count() + 1, 4, '0', STR_PAD_LEFT);

        return view('delivery-orders.create', compact('so', 'availableSOs', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'do_number' => 'required|string|max:50|unique:delivery_orders,do_number',
            'sales_order_id' => 'nullable|exists:sales_orders,id',
            'date' => 'required|date',
            'shipping_address' => 'nullable|string|max:255',
            'driver_name' => 'nullable|string|max:255',
            'vehicle_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sales_order_item_id' => 'nullable|exists:sales_order_items,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.warehouse_id' => 'nullable|exists:warehouses,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($data) {
            $so = !empty($data['sales_order_id']) ? SalesOrder::find($data['sales_order_id']) : null;

            $do = DeliveryOrder::create([
                'company_id' => session('company_id'),
                'sales_order_id' => $data['sales_order_id'] ?? null,
                'do_number' => $data['do_number'],
                'customer_id' => $so?->customer_id,
                'date' => $data['date'],
                'shipping_address' => $data['shipping_address'] ?? null,
                'driver_name' => $data['driver_name'] ?? null,
                'vehicle_number' => $data['vehicle_number'] ?? null,
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $do->items()->create([
                    'sales_order_item_id' => $item['sales_order_item_id'] ?? null,
                    'product_id' => $item['product_id'] ?? null,
                    'warehouse_id' => $item['warehouse_id'] ?? null,
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                ]);
            }
        });

        return redirect()->route('delivery-orders.index')->with('success', 'DO berhasil dibuat.');
    }

    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['customer', 'salesOrder', 'items.product', 'items.warehouse']);
        return view('delivery-orders.show', ['do' => $deliveryOrder]);
    }

    public function destroy(DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->status !== 'draft') return back()->with('error', 'Hanya DO draft yang bisa dihapus.');
        $deliveryOrder->items()->delete();
        $deliveryOrder->delete();
        return redirect()->route('delivery-orders.index')->with('success', 'DO dihapus.');
    }

    /**
     * Tandai DO sudah dikirim → kurangi stok, update SO delivered_qty.
     */
    public function deliver(DeliveryOrder $deliveryOrder, StockService $stockService)
    {
        if ($deliveryOrder->status === 'delivered') return back()->with('error', 'DO sudah dikirim.');

        DB::transaction(function () use ($deliveryOrder, $stockService) {
            foreach ($deliveryOrder->items as $item) {
                // Kurangi stok
                if ($item->product_id && $item->warehouse_id) {
                    $product = Product::find($item->product_id);
                    if ($product && $product->track_stock) {
                        $stockService->stockOut($product->id, $item->warehouse_id, $item->qty, [
                            'date' => $deliveryOrder->date,
                            'type' => 'out',
                            'reference' => $deliveryOrder->do_number,
                            'description' => 'Pengiriman ' . $deliveryOrder->do_number,
                            'source_type' => 'delivery_order',
                            'source_id' => $deliveryOrder->id,
                        ]);
                    }
                }

                // Update delivered_qty di SO item
                if ($item->sales_order_item_id) {
                    $soItem = \App\Models\SalesOrderItem::find($item->sales_order_item_id);
                    if ($soItem) {
                        $soItem->increment('delivered_qty', $item->qty);
                    }
                }
            }

            $deliveryOrder->update([
                'status' => 'delivered',
                'delivered_by' => auth()->id(),
                'delivered_at' => now(),
            ]);

            // Update status SO
            if ($deliveryOrder->salesOrder) {
                $so = $deliveryOrder->salesOrder->fresh();
                $so->update(['status' => $so->isFullyDelivered() ? 'delivered' : 'partial_delivered']);
            }
        });

        return back()->with('success', 'DO berhasil dikirim, stok berkurang.');
    }

    /**
     * Buat Invoice dari DO.
     */
    public function createInvoice(DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->status !== 'delivered') {
            return back()->with('error', 'DO harus dikirim dulu sebelum dibuat invoice.');
        }

        $so = $deliveryOrder->salesOrder;
        if (!$so) return back()->with('error', 'DO tidak terkait dengan SO.');

        // Buat invoice dari item DO (harga dari SO item)
        $items = $deliveryOrder->items->map(function ($doItem) {
            $soItem = $doItem->sales_order_item_id ? \App\Models\SalesOrderItem::find($doItem->sales_order_item_id) : null;
            return [
                'product_id' => $doItem->product_id,
                'warehouse_id' => $doItem->warehouse_id,
                'account_id' => $soItem?->account_id,
                'description' => $doItem->description,
                'qty' => $doItem->qty,
                'price' => $soItem?->price ?? 0,
                'discount' => 0,
            ];
        })->toArray();

        return redirect()->route('sales.create', ['from_do' => $deliveryOrder->id]);
    }
}