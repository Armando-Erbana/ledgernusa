<?php
namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::orderBy('name')->get();
        return view('warehouses.index', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ]);
        $data['company_id'] = session('company_id');
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Warehouse::where('company_id', session('company_id'))->update(['is_default' => false]);
        }

        Warehouse::create($data);
        return redirect()->route('warehouses.index')->with('success', 'Gudang ditambahkan.');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ]);
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Warehouse::where('company_id', $warehouse->company_id)->where('id', '!=', $warehouse->id)->update(['is_default' => false]);
        }

        $warehouse->update($data);
        return redirect()->route('warehouses.index')->with('success', 'Gudang diperbarui.');
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->movements()->exists()) {
            return back()->with('error', 'Gudang sudah ada transaksi. Tidak bisa dihapus.');
        }
        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success', 'Gudang dihapus.');
    }
}