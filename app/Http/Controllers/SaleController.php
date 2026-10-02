<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with('customer')->latest('date')->latest('id');
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $sales = $query->paginate(20)->withQueryString();
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $revenueAccounts = Account::where('type', 'revenue')->where('is_active', true)->orderBy('code')->get();
        $nextNumber = 'INV-' . date('Ym') . '-' . str_pad(Sale::where('company_id', session('company_id'))->count() + 1, 4, '0', STR_PAD_LEFT);
        return view('sales.create', compact('customers', 'revenueAccounts', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|max:50',
            'customer_id' => 'nullable|exists:customers,id',
            'date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:date',
            'type' => 'required|in:cash,credit',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.account_id' => 'required|exists:accounts,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.warehouse_id' => 'nullable|exists:warehouses,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($data['items'] as $i) {
            $subtotal += ($i['qty'] * $i['price']) - ($i['discount'] ?? 0);
        }
        $discount = $data['discount'] ?? 0;
        $tax = $data['tax'] ?? 0;
        $total = $subtotal - $discount + $tax;

        DB::transaction(function () use ($data, $subtotal, $discount, $tax, $total) {
            $sale = Sale::create([
                'company_id' => session('company_id'),
                'invoice_number' => $data['invoice_number'],
                'customer_id' => $data['customer_id'] ?? null,
                'date' => $data['date'],
                'due_date' => $data['due_date'] ?? null,
                'type' => $data['type'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'status' => $data['type'] === 'cash' ? 'paid' : 'unpaid',
                'paid_amount' => $data['type'] === 'cash' ? $total : 0,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $stockService = new StockService();

            foreach ($data['items'] as $item) {
                $itemSubtotal = ($item['qty'] * $item['price']) - ($item['discount'] ?? 0);
                $product = !empty($item['product_id']) ? Product::find($item['product_id']) : null;
                $costPrice = $product ? (float) $product->cost_price : 0;
                $totalCost = $item['qty'] * $costPrice;

                $sale->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'warehouse_id' => $item['warehouse_id'] ?? null,
                    'account_id' => $item['account_id'],
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $itemSubtotal,
                    'cost_price' => $costPrice,
                    'total_cost' => $totalCost,
                ]);

                // Kurangi stok kalau ada produk
                if ($product && $product->track_stock && !empty($item['warehouse_id'])) {
                    $stockService->stockOut(
                        $product->id,
                        $item['warehouse_id'],
                        $item['qty'],
                        [
                            'date' => $data['date'],
                            'type' => 'out',
                            'reference' => $data['invoice_number'],
                            'description' => 'Penjualan ' . $data['invoice_number'],
                            'source_type' => 'sale',
                            'source_id' => $sale->id,
                        ]
                    );
                }
            }

            $journal = $sale->generateJournal();
            $sale->update(['journal_id' => $journal->id]);
        });

        return redirect()->route('sales.index')->with('success', 'Invoice berhasil dibuat.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'items.account', 'items.product', 'payments.cashAccount', 'journal.entries.account']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        if ($sale->status !== 'draft' && $sale->status !== 'unpaid') abort(403);
        $customers = Customer::orderBy('name')->get();
        $revenueAccounts = Account::where('type', 'revenue')->where('is_active', true)->orderBy('code')->get();
        $sale->load('items');
        return view('sales.edit', compact('sale', 'customers', 'revenueAccounts'));
    }

    public function update(Request $request, Sale $sale)
    {
        if ($sale->status === 'paid') abort(403);

        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.account_id' => 'required|exists:accounts,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.warehouse_id' => 'nullable|exists:warehouses,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($data['items'] as $i) {
            $subtotal += ($i['qty'] * $i['price']) - ($i['discount'] ?? 0);
        }
        $discount = $data['discount'] ?? 0;
        $tax = $data['tax'] ?? 0;
        $total = $subtotal - $discount + $tax;

        DB::transaction(function () use ($sale, $data, $subtotal, $discount, $tax, $total) {
            $stockService = new StockService();

            // Kembalikan stok lama sebelum update
            foreach ($sale->items as $oldItem) {
                if ($oldItem->product_id && $oldItem->warehouse_id) {
                    $product = Product::find($oldItem->product_id);
                    if ($product && $product->track_stock) {
                        $stockService->stockIn(
                            $product->id,
                            $oldItem->warehouse_id,
                            $oldItem->qty,
                            $oldItem->cost_price,
                            [
                                'date' => now(),
                                'type' => 'in',
                                'reference' => 'CANCEL-' . $sale->invoice_number,
                                'description' => 'Koreksi edit invoice',
                                'source_type' => 'sale_edit_cancel',
                                'source_id' => $sale->id,
                            ]
                        );
                    }
                }
            }

            // Hapus jurnal & item lama
            if ($sale->journal) {
                $sale->journal->entries()->delete();
                $sale->journal->delete();
            }
            $sale->items()->delete();

            // Update sale
            $sale->update([
                'customer_id' => $data['customer_id'] ?? null,
                'date' => $data['date'],
                'due_date' => $data['due_date'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
            ]);

            // Buat item baru + kurangi stok
            foreach ($data['items'] as $item) {
                $itemSubtotal = ($item['qty'] * $item['price']) - ($item['discount'] ?? 0);
                $product = !empty($item['product_id']) ? Product::find($item['product_id']) : null;
                $costPrice = $product ? (float) $product->cost_price : 0;
                $totalCost = $item['qty'] * $costPrice;

                $sale->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'warehouse_id' => $item['warehouse_id'] ?? null,
                    'account_id' => $item['account_id'],
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $itemSubtotal,
                    'cost_price' => $costPrice,
                    'total_cost' => $totalCost,
                ]);

                if ($product && $product->track_stock && !empty($item['warehouse_id'])) {
                    $stockService->stockOut(
                        $product->id,
                        $item['warehouse_id'],
                        $item['qty'],
                        [
                            'date' => $data['date'],
                            'type' => 'out',
                            'reference' => $sale->invoice_number,
                            'description' => 'Penjualan ' . $sale->invoice_number,
                            'source_type' => 'sale',
                            'source_id' => $sale->id,
                        ]
                    );
                }
            }

            // Regenerate jurnal
            $journal = $sale->generateJournal();
            $sale->update(['journal_id' => $journal->id]);
        });

        return redirect()->route('sales.index')->with('success', 'Invoice diperbarui.');
    }

    public function destroy(Sale $sale)
    {
        if ($sale->paid_amount > 0) {
            return back()->with('error', 'Invoice sudah ada pembayaran, tidak bisa dihapus.');
        }

        DB::transaction(function () use ($sale) {
            $stockService = new StockService();

            // Kembalikan stok sebelum hapus
            foreach ($sale->items as $oldItem) {
                if ($oldItem->product_id && $oldItem->warehouse_id) {
                    $product = Product::find($oldItem->product_id);
                    if ($product && $product->track_stock) {
                        $stockService->stockIn(
                            $product->id,
                            $oldItem->warehouse_id,
                            $oldItem->qty,
                            $oldItem->cost_price,
                            [
                                'date' => now(),
                                'type' => 'in',
                                'reference' => 'CANCEL-' . $sale->invoice_number,
                                'description' => 'Koreksi hapus invoice',
                                'source_type' => 'sale_delete_cancel',
                                'source_id' => $sale->id,
                            ]
                        );
                    }
                }
            }

            if ($sale->journal) {
                $sale->journal->entries()->delete();
                $sale->journal->delete();
            }
            $sale->items()->delete();
            $sale->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Invoice dihapus.');
    }
}