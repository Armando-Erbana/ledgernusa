<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->q.'%')->orWhere('code', 'like', '%'.$request->q.'%');
            });
        }
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->get('low_stock') === '1') $query->whereColumn('stock', '<=', 'min_stock');

        $products = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = ProductCategory::orderBy('name')->get();
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        $accounts = Account::where('company_id', session('company_id'))->where('is_active', true)->orderBy('code')->get();
        return view('products.create', compact('categories', 'accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'unit' => 'required|string|max:20',
            'description' => 'nullable|string',
            'inventory_account_id' => 'nullable|exists:accounts,id',
            'sales_account_id' => 'nullable|exists:accounts,id',
            'cogs_account_id' => 'nullable|exists:accounts,id',
            'cost_price' => 'nullable|numeric|min:0',
            'sell_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'track_stock' => 'nullable|boolean',
        ]);
        $data['company_id'] = session('company_id');
        $data['track_stock'] = $request->boolean('track_stock', true);
        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Produk ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load('category', 'movements.warehouse');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('name')->get();
        $accounts = Account::where('company_id', session('company_id'))->where('is_active', true)->orderBy('code')->get();
        return view('products.edit', compact('product', 'categories', 'accounts'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'unit' => 'required|string|max:20',
            'description' => 'nullable|string',
            'inventory_account_id' => 'nullable|exists:accounts,id',
            'sales_account_id' => 'nullable|exists:accounts,id',
            'cogs_account_id' => 'nullable|exists:accounts,id',
            'cost_price' => 'nullable|numeric|min:0',
            'sell_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'track_stock' => 'nullable|boolean',
        ]);
        $data['track_stock'] = $request->boolean('track_stock', true);
        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk dihapus.');
    }
}