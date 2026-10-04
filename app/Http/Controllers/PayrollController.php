<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::latest('period_year')->latest('period_month')->paginate(20);
        return view('payrolls.index', compact('payrolls'));
    }

    public function create()
    {
        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        $nextNumber = 'PAY-' . date('Ym') . '-' . str_pad(Payroll::where('company_id', session('company_id'))->count() + 1, 4, '0', STR_PAD_LEFT);
        return view('payrolls.create', compact('employees', 'nextNumber'));
    }

    public function store(Request $request, PayrollService $service)
    {
        $data = $request->validate([
            'payroll_number' => 'required|string|max:50|unique:payrolls,payroll_number',
            'period_month' => 'required|integer|between:1,12',
            'period_year' => 'required|integer|min:2020',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.employee_id' => 'required|exists:employees,id',
            'items.*.basic_salary' => 'required|numeric|min:0',
            'items.*.allowance' => 'nullable|numeric|min:0',
            'items.*.other_income' => 'nullable|numeric|min:0',
            'items.*.other_deduction' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $service) {
            $payroll = Payroll::create([
                'company_id' => session('company_id'),
                'payroll_number' => $data['payroll_number'],
                'period_month' => $data['period_month'],
                'period_year' => $data['period_year'],
                'payment_date' => $data['payment_date'],
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $totalGross = 0; $totalPph = 0; $totalBpjs = 0; $totalDed = 0; $totalNet = 0;

            foreach ($data['items'] as $item) {
                $employee = Employee::find($item['employee_id']);
                $calc = $service->calculateItem($employee, [
                    'basic_salary' => $item['basic_salary'],
                    'allowance' => $item['allowance'] ?? 0,
                    'other_income' => $item['other_income'] ?? 0,
                    'other_deduction' => $item['other_deduction'] ?? 0,
                ]);

                $calc['payroll_id'] = $payroll->id;
                PayrollItem::create($calc);

                $totalGross += $calc['gross_salary'];
                $totalPph += $calc['pph21'];
                $totalBpjs += $calc['bpjs_kesehatan'] + $calc['bpjs_ketenagakerjaan'];
                $totalDed += $calc['total_deduction'];
                $totalNet += $calc['net_salary'];
            }

            $payroll->update([
                'total_gross' => $totalGross,
                'total_pph21' => $totalPph,
                'total_bpjs' => $totalBpjs,
                'total_deduction' => $totalDed,
                'total_net' => $totalNet,
            ]);
        });

        return redirect()->route('payrolls.index')->with('success', 'Payroll berhasil dibuat.');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load('items.employee', 'journal.entries.account');
        return view('payrolls.show', compact('payroll'));
    }

    public function destroy(Payroll $payroll)
    {
        if ($payroll->status !== 'draft') return back()->with('error', 'Hanya payroll draft yang bisa dihapus.');
        $payroll->items()->delete();
        $payroll->delete();
        return redirect()->route('payrolls.index')->with('success', 'Payroll dihapus.');
    }

    public function post(Payroll $payroll)
    {
        if ($payroll->status !== 'draft') return back()->with('error', 'Payroll sudah diposting.');

        DB::transaction(function () use ($payroll) {
            $journal = $payroll->generateJournal();
            $payroll->update([
                'journal_id' => $journal->id,
                'status' => 'posted',
            ]);
        });

        return back()->with('success', 'Payroll diposting, jurnal otomatis dibuat.');
    }

    public function markPaid(Payroll $payroll)
    {
        if ($payroll->status !== 'posted') return back()->with('error', 'Payroll harus diposting dulu.');
        $payroll->update(['status' => 'paid']);
        return back()->with('success', 'Payroll ditandai sudah dibayar.');
    }

    public function slipGaji(Payroll $payroll, PayrollItem $item)
    {
        $item->load('employee');
        return view('payrolls.slip', compact('payroll', 'item'));
    }
}