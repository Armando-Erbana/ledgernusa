<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\PayrollItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index()
    {
        $isOwner = auth()->user()->roleInActiveCompany() !== 'staff';
        $branchId = session('branch_id');

        $query = Payroll::where('company_id', session('company_id'));

        if (!$isOwner) {
            $query->where('branch_id', $branchId);
        } elseif ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $payrolls = $query->latest('id')->paginate(20);
        return view('payrolls.index', compact('payrolls'));
    }

    public function create()
    {
        $isOwner = auth()->user()->roleInActiveCompany() !== 'staff';
        $branchId = session('branch_id');

        $query = Employee::where('company_id', session('company_id'))->where('is_active', true);

        if (!$isOwner) {
            $query->where('branch_id', $branchId);
        } elseif ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $employees = $query->orderBy('name')->get();
            
        $nextNumber = 'PAY-' . date('Ym') . '-' . str_pad(Payroll::where('company_id', session('company_id'))->count() + 1, 4, '0', STR_PAD_LEFT);
        
        return view('payrolls.create', compact('employees', 'nextNumber'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'payroll_number' => 'required|string|max:50',
            'period_month' => 'required|numeric',
            'period_year' => 'required|numeric',
            'payment_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.employee_id' => 'required|exists:employees,id',
            'items.*.basic_salary' => 'required|numeric',
            'items.*.allowance' => 'nullable|numeric',
            'items.*.other_income' => 'nullable|numeric',
            'items.*.other_deduction' => 'nullable|numeric',
        ]);

        try {
            DB::transaction(function () use ($data, $request) {
                $totalGross = 0; $totalPph21 = 0; $totalBpjs = 0; $totalNet = 0;
                $payrollItems = [];

                foreach ($data['items'] as $item) {
                    $gross = $item['basic_salary'] + ($item['allowance'] ?? 0) + ($item['other_income'] ?? 0);
                    $pph21 = 0; $bpjs = 0; 
                    $net = $gross - $pph21 - $bpjs - ($item['other_deduction'] ?? 0);

                    $totalGross += $gross;
                    $totalPph21 += $pph21;
                    $totalBpjs += $bpjs;
                    $totalNet += $net;

                    $payrollItems[] = [
                        'employee_id' => $item['employee_id'],
                        'basic_salary' => $item['basic_salary'],
                        'allowance' => $item['allowance'] ?? 0,
                        'other_income' => $item['other_income'] ?? 0,
                        'other_deduction' => $item['other_deduction'] ?? 0,
                        'gross_salary' => $gross,
                        'pph21' => $pph21,
                        'bpjs_kesehatan' => $bpjs / 2,
                        'bpjs_ketenagakerjaan' => $bpjs / 2,
                        'total_deduction' => $pph21 + $bpjs + ($item['other_deduction'] ?? 0),
                        'net_salary' => $net,
                    ];
                }

                // Handle branch_id untuk Konsolidasi
                $branchId = session('branch_id');
                if (!$branchId) {
                    $mainBranch = \App\Models\Branch::where('company_id', session('company_id'))->where('is_main', true)->first();
                    $branchId = $mainBranch ? $mainBranch->id : null;
                }

                $payroll = Payroll::create([
                    'company_id' => session('company_id'),
                    'branch_id' => $branchId,
                    'payroll_number' => $data['payroll_number'],
                    'period_month' => $data['period_month'],
                    'period_year' => $data['period_year'],
                    'payment_date' => $data['payment_date'],
                    'total_gross' => $totalGross,
                    'total_pph21' => $totalPph21,
                    'total_bpjs' => $totalBpjs,
                    'total_net' => $totalNet,
                    'status' => 'draft',
                    'notes' => $request->notes,
                ]);

                foreach ($payrollItems as $pi) {
                    $payroll->items()->create($pi);
                }
            });

            return redirect()->route('payrolls.index')->with('success', 'Payroll berhasil dibuat sebagai Draft.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan Payroll: ' . $e->getMessage());
        }
    }

    public function show(Payroll $payroll)
    {
        abort_if(auth()->user()->roleInActiveCompany() === 'staff' && $payroll->branch_id !== session('branch_id'), 403);
        $payroll->load('items.employee', 'journal');
        return view('payrolls.show', compact('payroll'));
    }

    public function post(Payroll $payroll)
    {
        abort_if(auth()->user()->roleInActiveCompany() === 'staff' && $payroll->branch_id !== session('branch_id'), 403);

        if ($payroll->status === 'posted') {
            return back()->with('error', 'Payroll sudah di-posting.');
        }

        try {
            DB::transaction(function () use ($payroll) {
                $payroll->update(['status' => 'posted']);
                $journal = $payroll->generateJournal();
                
                if ($journal) {
                    $payroll->update(['journal_id' => $journal->id]);
                }
            });

            return back()->with('success', 'Payroll berhasil diposting & Jurnal telah dibuat.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function markPaid(Payroll $payroll)
    {
        abort_if(auth()->user()->roleInActiveCompany() === 'staff' && $payroll->branch_id !== session('branch_id'), 403);

        if ($payroll->status !== 'posted') {
            return back()->with('error', 'Hanya payroll berstatus posted yang bisa dibayar.');
        }
        $payroll->update(['status' => 'paid']);
        return back()->with('success', 'Payroll berhasil ditandai sudah dibayar.');
    }

    public function destroy(Payroll $payroll)
    {
        abort_if(auth()->user()->roleInActiveCompany() === 'staff' && $payroll->branch_id !== session('branch_id'), 403);

        if ($payroll->status === 'paid') {
            return back()->with('error', 'Payroll yang sudah dibayar tidak dapat dihapus.');
        }

        try {
            DB::transaction(function () use ($payroll) {
                if ($payroll->journal) {
                    $payroll->journal->entries()->delete();
                    $payroll->journal->delete();
                }
                $payroll->items()->delete();
                $payroll->delete();
            });
            return redirect()->route('payrolls.index')->with('success', 'Data Payroll berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus Payroll: ' . $e->getMessage());
        }
    }

    public function slipGaji(Payroll $payroll, PayrollItem $item)
    {
        abort_if(auth()->user()->roleInActiveCompany() === 'staff' && $payroll->branch_id !== session('branch_id'), 403);
        return view('payrolls.slip', compact('payroll', 'item'));
    }
}