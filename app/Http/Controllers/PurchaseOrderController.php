<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with('supplier')->latest('date')->latest('id');
        if ($request->filled('status')) $query->where('status', $request->status);
        $orders = $query->paginate(20)->withQueryString();
        return view('purchase-orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $expenseAccounts = Account::whereIn('type', ['expense', 'asset'])
            ->where('is_cash', false)->where('is_bank', false)
            ->where('is_active', true)->orderBy('code')->get();
        $company = \App\Models\Company::find(session('company_id'));
        $nextNumber = 'PO-' . date('Ym') . '-' . str_pad(PurchaseOrder::where('company_id', session('company_id'))->count() + 1, 4, '0', STR_PAD_LEFT);
        return view('purchase-orders.create', compact('suppliers', 'expenseAccounts', 'company', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'po_number' => 'required|string|max:50|unique:purchase_orders,po_number',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.warehouse_id' => 'nullable|exists:warehouses,id',
            'items.*.account_id' => 'nullable|exists:accounts,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($data['items'] as $i) $subtotal += ($i['qty'] * $i['price']) - ($i['discount'] ?? 0);
        $discount = $data['discount'] ?? 0;
        $tax = $data['tax'] ?? 0;
        $total = $subtotal - $discount + $tax;

        DB::transaction(function () use ($data, $subtotal, $discount, $tax, $total) {
            $po = PurchaseOrder::create([
                'company_id' => session('company_id'),
                'po_number' => $data['po_number'],
                'supplier_id' => $data['supplier_id'] ?? null,
                'date' => $data['date'],
                'expected_date' => $data['expected_date'] ?? null,
                'status' => 'draft',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $itemSubtotal = ($item['qty'] * $item['price']) - ($item['discount'] ?? 0);
                $po->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'warehouse_id' => $item['warehouse_id'] ?? null,
                    'account_id' => $item['account_id'] ?? null,
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $itemSubtotal,
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')->with('success', 'PO berhasil dibuat.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product', 'goodsReceipts']);
        return view('purchase-orders.show', ['po' => $purchaseOrder]);
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') abort(403);
        $suppliers = Supplier::orderBy('name')->get();
        $expenseAccounts = Account::whereIn('type', ['expense', 'asset'])
            ->where('is_cash', false)->where('is_bank', false)
            ->where('is_active', true)->orderBy('code')->get();
        $purchaseOrder->load('items');
        return view('purchase-orders.edit', ['po' => $purchaseOrder, 'suppliers' => $suppliers, 'expenseAccounts' => $expenseAccounts]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') abort(403);

        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date' => 'required|date',
            'expected_date' => 'nullable|date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($data['items'] as $i) $subtotal += ($i['qty'] * $i['price']) - ($i['discount'] ?? 0);
        $discount = $data['discount'] ?? 0;
        $tax = $data['tax'] ?? 0;
        $total = $subtotal - $discount + $tax;

        DB::transaction(function () use ($purchaseOrder, $data, $subtotal, $discount, $tax, $total) {
            $purchaseOrder->items()->delete();
            $purchaseOrder->update([
                'supplier_id' => $data['supplier_id'] ?? null,
                'date' => $data['date'],
                'expected_date' => $data['expected_date'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
            ]);
            foreach ($data['items'] as $item) {
                $itemSubtotal = ($item['qty'] * $item['price']) - ($item['discount'] ?? 0);
                $purchaseOrder->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'warehouse_id' => $item['warehouse_id'] ?? null,
                    'account_id' => $item['account_id'] ?? null,
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $itemSubtotal,
                ]);
            }
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'PO diperbarui.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') return back()->with('error', 'Hanya PO draft yang bisa dihapus.');
        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')->with('success', 'PO dihapus.');
    }

    public function confirm(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') return back()->with('error', 'PO sudah dikonfirmasi.');
        $purchaseOrder->update([
            'status' => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);
        return back()->with('success', 'PO dikonfirmasi.');
    }
}