<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesOrder::with('customer')->latest('date')->latest('id');
        if ($request->filled('status')) $query->where('status', $request->status);
        $orders = $query->paginate(20)->withQueryString();
        return view('sales-orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $revenueAccounts = Account::where('type', 'revenue')->where('is_active', true)->orderBy('code')->get();
        $company = \App\Models\Company::find(session('company_id'));
        $nextNumber = 'SO-' . date('Ym') . '-' . str_pad(SalesOrder::where('company_id', session('company_id'))->count() + 1, 4, '0', STR_PAD_LEFT);
        return view('sales-orders.create', compact('customers', 'revenueAccounts', 'company', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'so_number' => 'required|string|max:50|unique:sales_orders,so_number',
            'customer_id' => 'nullable|exists:customers,id',
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
            $so = SalesOrder::create([
                'company_id' => session('company_id'),
                'so_number' => $data['so_number'],
                'customer_id' => $data['customer_id'] ?? null,
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
                $so->items()->create([
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

        return redirect()->route('sales-orders.index')->with('success', 'SO berhasil dibuat.');
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'items.product', 'deliveryOrders']);
        return view('sales-orders.show', ['so' => $salesOrder]);
    }

    public function edit(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') abort(403);
        $customers = Customer::orderBy('name')->get();
        $revenueAccounts = Account::where('type', 'revenue')->where('is_active', true)->orderBy('code')->get();
        $salesOrder->load('items');
        return view('sales-orders.edit', ['so' => $salesOrder, 'customers' => $customers, 'revenueAccounts' => $revenueAccounts]);
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') abort(403);

        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
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

        DB::transaction(function () use ($salesOrder, $data, $subtotal, $discount, $tax, $total) {
            $salesOrder->items()->delete();
            $salesOrder->update([
                'customer_id' => $data['customer_id'] ?? null,
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
                $salesOrder->items()->create([
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

        return redirect()->route('sales-orders.show', $salesOrder)->with('success', 'SO diperbarui.');
    }

    public function destroy(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') return back()->with('error', 'Hanya SO draft yang bisa dihapus.');
        $salesOrder->items()->delete();
        $salesOrder->delete();
        return redirect()->route('sales-orders.index')->with('success', 'SO dihapus.');
    }

    public function confirm(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') return back()->with('error', 'SO sudah dikonfirmasi.');
        $salesOrder->update([
            'status' => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);
        return back()->with('success', 'SO dikonfirmasi.');
    }
}