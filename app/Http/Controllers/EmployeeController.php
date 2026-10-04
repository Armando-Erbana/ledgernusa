<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::orderBy('name');
        if ($request->filled('q')) $query->where('name', 'like', '%'.$request->q.'%');
        if ($request->filled('status')) $query->where('is_active', $request->status === 'active');
        $employees = $query->paginate(20)->withQueryString();
        return view('employees.index', compact('employees'));
    }

    public function create() { return view('employees.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_number' => 'required|string|max:50',
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
        ]);
        $data['company_id'] = session('company_id');
        $data['fixed_allowance'] = $data['fixed_allowance'] ?? 0;
        $data['is_active'] = true;
        Employee::create($data);
        return redirect()->route('employees.index')->with('success', 'Karyawan ditambahkan.');
    }

    public function edit(Employee $employee) { return view('employees.edit', compact('employee')); }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'employee_number' => 'required|string|max:50',
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
        ]);
        $data['fixed_allowance'] = $data['fixed_allowance'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);
        $employee->update($data);
        return redirect()->route('employees.index')->with('success', 'Karyawan diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Karyawan dihapus.');
    }
}