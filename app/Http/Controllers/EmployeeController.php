<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        // Cek jika yang login adalah Owner/Admin dan sedang di mode Konsolidasi
        $isOwner = auth()->user()->roleInActiveCompany() !== 'staff';
        $branchId = session('branch_id');

        $query = Employee::where('company_id', session('company_id'));

        // Jika bukan Owner (berarti Staff), filter berdasarkan cabangnya
        if (!$isOwner) {
            $query->where('branch_id', $branchId);
        } 
        // Jika Owner tapi sedang memilih cabang spesifik (bukan Konsolidasi)
        elseif ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $query->orderBy('name');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $employees = $query->paginate(20)->withQueryString();
        return view('employees.index', compact('employees'));
    }

    public function create() 
    { 
        return view('employees.create'); 
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_number' => [
                'required', 'string', 'max:50',
                Rule::unique('employees')->where(function ($query) {
                    return $query->where('company_id', session('company_id'));
                })
            ],
            'name' => 'required|string|max:255',
            'npwp' => 'nullable|string',
            'ktp' => 'nullable|string',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'join_date' => 'nullable|date',
            'employment_type' => 'required|in:permanent,contract,freelance',
            'ptkp_status' => 'required|string',
            'basic_salary' => 'required|numeric|min:0',
            'fixed_allowance' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
        ], [
            'employee_number.unique' => 'NIK / Nomor Karyawan ini sudah terdaftar di perusahaan Anda.',
        ]);

        $data['company_id'] = session('company_id');
        
        // Jika Owner sedang di mode Konsolidasi, paksa masuk ke cabang utama (Pusat)
        $branchId = session('branch_id');
        if (!$branchId) {
            $mainBranch = \App\Models\Branch::where('company_id', session('company_id'))->where('is_main', true)->first();
            $branchId = $mainBranch ? $mainBranch->id : null;
        }
        
        $data['branch_id'] = $branchId;
        $data['fixed_allowance'] = $data['fixed_allowance'] ?? 0;
        $data['is_active'] = true;

        Employee::create($data);
        
        return redirect()->route('employees.index')->with('success', 'Karyawan ditambahkan.');
    }

    public function edit(Employee $employee) 
    { 
        // PERBAIKAN: Hanya blokir jika Staff mencoba buka cabang lain
        abort_if(auth()->user()->roleInActiveCompany() === 'staff' && $employee->branch_id !== session('branch_id'), 403);
        return view('employees.edit', compact('employee')); 
    }

    public function update(Request $request, Employee $employee)
    {
        abort_if(auth()->user()->roleInActiveCompany() === 'staff' && $employee->branch_id !== session('branch_id'), 403);

        $data = $request->validate([
            'employee_number' => [
                'required', 'string', 'max:50',
                Rule::unique('employees')->where(function ($query) {
                    return $query->where('company_id', session('company_id'));
                })->ignore($employee->id)
            ],
            'name' => 'required|string|max:255',
            'npwp' => 'nullable|string',
            'ktp' => 'nullable|string',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'join_date' => 'nullable|date',
            'resign_date' => 'nullable|date',
            'employment_type' => 'required|in:permanent,contract,freelance',
            'ptkp_status' => 'required|string',
            'basic_salary' => 'required|numeric|min:0',
            'fixed_allowance' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ], [
            'employee_number.unique' => 'NIK / Nomor Karyawan ini sudah terdaftar di perusahaan Anda.',
        ]);

        $data['fixed_allowance'] = $data['fixed_allowance'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);
        
        $employee->update($data);
        
        return redirect()->route('employees.index')->with('success', 'Karyawan diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        abort_if(auth()->user()->roleInActiveCompany() === 'staff' && $employee->branch_id !== session('branch_id'), 403);

        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Karyawan dihapus.');
    }
}