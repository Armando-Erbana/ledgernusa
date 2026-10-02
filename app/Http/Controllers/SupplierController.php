<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create() { return view('suppliers.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:30',
            'npwp' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'payment_term_days' => 'nullable|integer|min:0',
        ]);
        $data['company_id'] = session('company_id');
        Supplier::create($data);
        return redirect()->route('suppliers.index')->with('success', 'Supplier ditambahkan.');
    }

    public function edit(Supplier $supplier) { return view('suppliers.edit', compact('supplier')); }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:30',
            'npwp' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'payment_term_days' => 'nullable|integer|min:0',
        ]);
        $supplier->update($data);
        return redirect()->route('suppliers.index')->with('success', 'Supplier diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier dihapus.');
    }
}