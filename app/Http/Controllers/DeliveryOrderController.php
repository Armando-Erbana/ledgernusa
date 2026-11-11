<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Models\Sale;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryOrderController extends Controller
{
    public function index()
    {
        $dos = DeliveryOrder::with('salesOrder.customer')->latest('date')->latest('id')->paginate(20);
        return view('delivery-orders.index', compact('dos'));
    }

    public function create(Request $request)
    {
        $soId = $request->query('so_id');
        $so = null;

        if ($soId) {
            $so = SalesOrder::with(['customer', 'items.product', 'items.warehouse'])
                ->where('status', 'confirmed')
                ->findOrFail($soId);
        }

        $confirmedSOs = SalesOrder::where('status', 'confirmed')
            ->doesntHave('deliveryOrders')
            ->orWhereHas('deliveryOrders', function($q) {
                $q->where('status', 'draft');
            })
            ->get();

        $warehouses = Warehouse::where('company_id', session('company_id'))->get();
        
        $prefix = 'DO-' . date('Ym') . '-';
        $lastDO = DeliveryOrder::where('company_id', session('company_id'))->where('do_number', 'like', $prefix.'%')->orderBy('do_number', 'desc')->first();
        $nextNumber = $lastDO ? $prefix . str_pad((int) substr($lastDO->do_number, -4) + 1, 4, '0', STR_PAD_LEFT) : $prefix . '0001';

        return view('delivery-orders.create', compact('so', 'confirmedSOs', 'warehouses', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sales_order_item_id' => 'required|exists:sales_order_items,id',
            'items.*.warehouse_id' => 'required|exists:warehouses,id',
            'items.*.qty' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($data) {
            $so = SalesOrder::findOrFail($data['sales_order_id']);
            
            $prefix = 'DO-' . date('Ym') . '-';
            $lastDO = DeliveryOrder::where('company_id', session('company_id'))->where('do_number', 'like', $prefix.'%')->orderBy('do_number', 'desc')->lockForUpdate()->first();
            $doNumber = $lastDO ? $prefix . str_pad((int) substr($lastDO->do_number, -4) + 1, 4, '0', STR_PAD_LEFT) : $prefix . '0001';

            $do = DeliveryOrder::create([
                'company_id' => session('company_id'),
                'branch_id' => session('branch_id'),
                'sales_order_id' => $so->id,
                'do_number' => $doNumber,
                'date' => $data['date'],
                'status' => 'delivered',
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $stockService = new StockService();

            foreach ($data['items'] as $item) {
                $soItem = $so->items()->findOrFail($item['sales_order_item_id']);
                
                $do->items()->create([
                    'sales_order_item_id' => $soItem->id,
                    'product_id' => $soItem->product_id,
                    'warehouse_id' => $item['warehouse_id'],
                    'qty' => $item['qty'],
                    'description' => $soItem->description,
                ]);

                $soItem->increment('delivered_qty', $item['qty']);

                if ($soItem->product_id) {
                    $stockService->stockOut(
                        $soItem->product_id,
                        $item['warehouse_id'],
                        $item['qty'],
                        [
                            'date' => $data['date'],
                            'type' => 'out',
                            'reference' => $doNumber,
                            'description' => 'Pengiriman Barang ' . $doNumber,
                            'source_type' => 'delivery_order',
                            'source_id' => $do->id,
                        ]
                    );
                }
            }

            if ($so->isFullyDelivered()) {
                $so->update(['status' => 'invoiced']);
            }
        });

        return redirect()->route('delivery-orders.index')->with('success', 'Surat Jalan berhasil dibuat & stok berkurang.');
    }

    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['salesOrder.customer', 'items.product', 'items.warehouse']);
        return view('delivery-orders.show', compact('deliveryOrder'));
    }

    public function createInvoice(DeliveryOrder $deliveryOrder)
    {
        if (Sale::where('delivery_order_id', $deliveryOrder->id)->exists()) {
            return back()->with('error', 'Surat Jalan ini sudah ditagihkan sebelumnya.');
        }

        DB::transaction(function () use ($deliveryOrder) {
            $prefix = 'INV-' . date('Ym') . '-';
            $lastSale = Sale::where('company_id', session('company_id'))
                ->where('invoice_number', 'like', $prefix . '%')
                ->orderBy('invoice_number', 'desc')
                ->lockForUpdate()
                ->first();

            $invoiceNumber = $lastSale 
                ? $prefix . str_pad((int) substr($lastSale->invoice_number, -4) + 1, 4, '0', STR_PAD_LEFT) 
                : $prefix . '0001';

            $subtotal = 0;
            foreach ($deliveryOrder->items as $item) {
                $subtotal += ($item->qty * $item->salesOrderItem->price);
            }

            $sale = Sale::create([
                'company_id' => session('company_id'),
                'branch_id' => session('branch_id'),
                'delivery_order_id' => $deliveryOrder->id,
                'customer_id' => $deliveryOrder->salesOrder->customer_id,
                'invoice_number' => $invoiceNumber,
                'date' => now(),
                'due_date' => now()->addDays(30),
                'type' => 'credit',
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'status' => 'unpaid',
                'paid_amount' => 0,
                'notes' => 'Auto-generated from DO: ' . $deliveryOrder->do_number,
                'created_by' => auth()->id(),
            ]);

            foreach ($deliveryOrder->items as $item) {
                $sale->items()->create([
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'qty' => $item->qty,
                    'price' => $item->salesOrderItem->price,
                    'discount' => 0,
                    'subtotal' => ($item->qty * $item->salesOrderItem->price),
                ]);
            }

            $journal = $sale->generateJournal();
            $sale->update(['journal_id' => $journal->id]);

            $deliveryOrder->salesOrder->update(['status' => 'invoiced']);
        });

        return redirect()->route('sales.index')->with('success', 'Invoice berhasil dibuat dari Surat Jalan ' . $deliveryOrder->do_number);
    }

    public function destroy(DeliveryOrder $deliveryOrder)
    {
        return back()->with('error', 'Surat jalan tidak dapat dihapus. Gunakan retur jika ada kesalahan.');
    }
}