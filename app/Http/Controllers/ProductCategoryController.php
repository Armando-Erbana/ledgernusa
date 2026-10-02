<?php
namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::withCount('products')->orderBy('name')->get();
        return view('products.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $data['company_id'] = session('company_id');
        ProductCategory::create($data);
        return redirect()->route('product-categories.index')->with('success', 'Kategori ditambahkan.');
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $productCategory->update($data);
        return redirect()->route('product-categories.index')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();
        return redirect()->route('product-categories.index')->with('success', 'Kategori dihapus.');
    }
}