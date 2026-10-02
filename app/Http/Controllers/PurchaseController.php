<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Company;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier')->latest('date')->latest('id');
        if ($request->filled('status')) $query->where('status', $request->get('status'));
        $purchases = $query->paginate(20)->withQueryString();
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $expenseAccounts = Account::whereIn('type', ['expense', 'asset'])
            ->where('is_cash', false)->where('is_bank', false)
            ->where('is_active', true)->orderBy('code')->get();
        $company = Company::find(session('company_id'));
        $nextNumber = 'BILL-' . date('Ym') . '-' . str_pad(Purchase::where('company_id', session('company_id'))->count() + 1, 4, '0', STR_PAD_LEFT);

        return view('purchases.create', compact('suppliers', 'expenseAccounts', 'company', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bill_number' => 'required|string|max:50',
            'supplier_id' => 'nullable|exists:suppliers,id',
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
            $purchase = Purchase::create([
                'company_id' => session('company_id'),
                'bill_number' => $data['bill_number'],
                'supplier_id' => $data['supplier_id'] ?? null,
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
                $purchase->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'warehouse_id' => $item['warehouse_id'] ?? null,
                    'account_id' => $item['account_id'],
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $itemSubtotal,
                ]);

                // Tambah stok kalau ada produk
                if (!empty($item['product_id']) && !empty($item['warehouse_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->track_stock) {
                        $stockService->stockIn(
                            $product->id,
                            $item['warehouse_id'],
                            $item['qty'],
                            $item['price'],
                            [
                                'date' => $data['date'],
                                'type' => 'in',
                                'reference' => $data['bill_number'],
                                'description' => 'Pembelian ' . $data['bill_number'],
                                'source_type' => 'purchase',
                                'source_id' => $purchase->id,
                            ]
                        );
                    }
                }
            }

            $journal = $purchase->generateJournal();
            $purchase->update(['journal_id' => $journal->id]);
        });

        return redirect()->route('purchases.index')->with('success', 'Bill pembelian berhasil dibuat.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.account', 'items.product', 'payments.cashAccount', 'journal.entries.account']);
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        if ($purchase->status === 'paid') abort(403);
        $suppliers = Supplier::orderBy('name')->get();
        $expenseAccounts = Account::whereIn('type', ['expense', 'asset'])
            ->where('is_cash', false)->where('is_bank', false)->where('is_active', true)->orderBy('code')->get();
        $purchase->load('items');
        return view('purchases.edit', compact('purchase', 'suppliers', 'expenseAccounts'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->status === 'paid') abort(403);

        $data = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
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

        DB::transaction(function () use ($purchase, $data, $subtotal, $discount, $tax, $total) {
            $stockService = new StockService();

            // Kembalikan (kurangi) stok lama sebelum update
            foreach ($purchase->items as $oldItem) {
                if ($oldItem->product_id && $oldItem->warehouse_id) {
                    $product = Product::find($oldItem->product_id);
                    if ($product && $product->track_stock) {
                        $stockService->stockOut(
                            $product->id,
                            $oldItem->warehouse_id,
                            $oldItem->qty,
                            [
                                'date' => now(),
                                'type' => 'out',
                                'reference' => 'CANCEL-' . $purchase->bill_number,
                                'description' => 'Koreksi edit pembelian',
                                'source_type' => 'purchase_edit_cancel',
                                'source_id' => $purchase->id,
                            ]
                        );
                    }
                }
            }

            // Hapus jurnal & item lama
            if ($purchase->journal) {
                $purchase->journal->entries()->delete();
                $purchase->journal->delete();
            }
            $purchase->items()->delete();

            // Update purchase
            $purchase->update([
                'supplier_id' => $data['supplier_id'] ?? null,
                'date' => $data['date'],
                'due_date' => $data['due_date'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
            ]);

            // Buat item baru + tambah stok
            foreach ($data['items'] as $item) {
                $itemSubtotal = ($item['qty'] * $item['price']) - ($item['discount'] ?? 0);
                $purchase->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'warehouse_id' => $item['warehouse_id'] ?? null,
                    'account_id' => $item['account_id'],
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $itemSubtotal,
                ]);

                if (!empty($item['product_id']) && !empty($item['warehouse_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->track_stock) {
                        $stockService->stockIn(
                            $product->id,
                            $item['warehouse_id'],
                            $item['qty'],
                            $item['price'],
                            [
                                'date' => $data['date'],
                                'type' => 'in',
                                'reference' => $purchase->bill_number,
                                'description' => 'Pembelian ' . $purchase->bill_number,
                                'source_type' => 'purchase',
                                'source_id' => $purchase->id,
                            ]
                        );
                    }
                }
            }

            // Regenerate jurnal
            $journal = $purchase->generateJournal();
            $purchase->update(['journal_id' => $journal->id]);
        });

        return redirect()->route('purchases.index')->with('success', 'Pembelian diperbarui.');
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->paid_amount > 0) {
            return back()->with('error', 'Sudah ada pembayaran, tidak bisa dihapus.');
        }

        DB::transaction(function () use ($purchase) {
            $stockService = new StockService();

            // Kurangi stok sebelum hapus (karena ini pembelian = stok masuk)
            foreach ($purchase->items as $oldItem) {
                if ($oldItem->product_id && $oldItem->warehouse_id) {
                    $product = Product::find($oldItem->product_id);
                    if ($product && $product->track_stock) {
                        $stockService->stockOut(
                            $product->id,
                            $oldItem->warehouse_id,
                            $oldItem->qty,
                            [
                                'date' => now(),
                                'type' => 'out',
                                'reference' => 'CANCEL-' . $purchase->bill_number,
                                'description' => 'Koreksi hapus pembelian',
                                'source_type' => 'purchase_delete_cancel',
                                'source_id' => $purchase->id,
                            ]
                        );
                    }
                }
            }

            if ($purchase->journal) {
                $purchase->journal->entries()->delete();
                $purchase->journal->delete();
            }
            $purchase->items()->delete();
            $purchase->delete();
        });

        return redirect()->route('purchases.index')->with('success', 'Pembelian dihapus.');
    }
}