<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Account;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('company_id');
        $branchId = session('branch_id');
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        // Ambil data budget
        $query = Budget::with('account')
            ->where('company_id', $companyId)
            ->where('period_month', $month)
            ->where('period_year', $year);

        // Filter cabang (jika Owner sedang di mode Konsolidasi, tampilkan semua budget)
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $budgets = $query->get();

        // Ambil akun bertipe Beban (Expense) & Pendapatan (Revenue) untuk dropdown
        $accounts = Account::where('company_id', $companyId)
            ->whereIn('type', ['expense', 'revenue'])
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('budgets.index', compact('budgets', 'accounts', 'month', 'year', 'branchId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer',
            'amount' => 'required|numeric|min:0',
        ]);

        $branchId = session('branch_id');
        if (!$branchId) {
            return back()->with('error', 'Pilih cabang spesifik terlebih dahulu di pojok kanan atas sebelum menginput budget.');
        }

        Budget::updateOrCreate(
            [
                'company_id' => session('company_id'),
                'branch_id' => $branchId,
                'account_id' => $request->account_id,
                'period_month' => $request->period_month,
                'period_year' => $request->period_year,
            ],
            [
                'amount' => $request->amount
            ]
        );

        return back()->with('success', 'Budget berhasil disimpan.');
    }

        public function report(Request $request)
    {
        $companyId = session('company_id');
        $branchId = session('branch_id');
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        // 1. Ambil semua data Budget untuk periode ini
        $query = Budget::with('account')
            ->where('company_id', $companyId)
            ->where('period_month', $month)
            ->where('period_year', $year);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $budgets = $query->get();

        // 2. Hitung Realisasi untuk masing-masing budget
        $reportData = $budgets->map(function ($budget) use ($month, $year, $branchId) {
            
            // Query untuk menjumlahkan realisasi dari Jurnal (JournalEntry)
            $actualQuery = \App\Models\JournalEntry::query()
                ->join('journals', 'journal_entries.journal_id', '=', 'journals.id')
                ->where('journals.company_id', $budget->company_id)
                ->where('journals.status', 'posted')
                ->where('journal_entries.account_id', $budget->account_id)
                ->whereMonth('journals.date', $month)
                ->whereYear('journals.date', $year);

            if ($branchId) {
                $actualQuery->where('journals.branch_id', $branchId);
            }

            // Tergantung tipenya, Beban dihitung dari Debit, Pendapatan dari Kredit
            if ($budget->account->type === 'expense') {
                $actual = $actualQuery->sum('journal_entries.debit') - $actualQuery->sum('journal_entries.credit');
            } else {
                $actual = $actualQuery->sum('journal_entries.credit') - $actualQuery->sum('journal_entries.debit');
            }

            $budget->actual = $actual;
            $budget->difference = $budget->amount - $actual; // Selisih
            $budget->percentage = $budget->amount > 0 ? ($actual / $budget->amount) * 100 : 0;
            
            return $budget;
        });

        return view('budgets.report', compact('reportData', 'month', 'year', 'branchId'));
    }

    public function destroy(Budget $budget)
    {
        abort_if($budget->company_id !== session('company_id'), 403);
        $budget->delete();
        return back()->with('success', 'Budget berhasil dihapus.');
    }
}