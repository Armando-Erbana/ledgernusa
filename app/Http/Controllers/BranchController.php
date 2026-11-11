<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $companyId = session('company_id');
        $branches = Branch::where('company_id', $companyId)->latest()->get();
        
        return view('branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'is_main' => 'boolean'
        ]);

        $companyId = session('company_id');

        // Jika diset sebagai cabang utama, lepas status utama dari cabang lain
        if ($request->has('is_main')) {
            Branch::where('company_id', $companyId)->update(['is_main' => false]);
        }

        Branch::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'code' => $request->code,
            'address' => $request->address,
            'is_main' => $request->has('is_main'),
        ]);

        return back()->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function destroy(Branch $branch)
    {
        abort_if($branch->company_id !== session('company_id'), 403);
        
        if (Branch::where('company_id', session('company_id'))->count() <= 1) {
            return back()->with('error', 'Tidak bisa menghapus satu-satunya cabang.');
        }

        $branch->delete();
        return back()->with('success', 'Cabang berhasil dihapus.');
    }

    // Diperbarui untuk menerima parameter $id (termasuk 0 untuk Konsolidasi)
    public function switchBranch($id) 
    {
        $user = auth()->user();
        $role = $user->roleInActiveCompany();

        // Jika ID 0, berarti mode Konsolidasi (Semua Cabang)
        if ($id == 0) {
            if ($role === 'staff') {
                abort(403, 'Staff tidak dapat mengakses mode konsolidasi.');
            }
            session(['branch_id' => null]); // Null berarti tampilkan semua
            return back()->with('success', 'Berhasil beralih ke mode Konsolidasi.');
        }

        $branch = Branch::findOrFail($id);
        abort_if($branch->company_id !== session('company_id'), 403);

        // Jika user adalah Staff, pastikan dia hanya bisa switch ke cabang miliknya
        if ($role === 'staff') {
            $hasAccess = $user->branches()->where('branch_id', $branch->id)->exists();
            if (!$hasAccess) {
                abort(403, 'Anda tidak memiliki akses ke cabang ini.');
            }
        }
        
        session(['branch_id' => $branch->id]);
        
        return back()->with('success', 'Berhasil beralih ke cabang ' . $branch->name);
    }
}                                           