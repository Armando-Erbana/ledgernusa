<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Company;
use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClosingController extends Controller
{
    public function index()
    {
        $company = Company::find(session('company_id'));
        return view('closing.index', compact('company'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $year = $request->year;
        $companyId = session('company_id');
        $company = Company::findOrFail($companyId);

        if ($company->closed_until && $company->closed_until->year >= $year) {
            return back()->with('error', "Tahun $year sudah ditutup atau melewati batas tutup buku sebelumnya.");
        }

        DB::beginTransaction();
        try {
            $startDate = "$year-01-01";
            $endDate = "$year-12-31";

            $revenues = Account::where('company_id', $companyId)
                ->where('type', 'revenue')
                ->withSum(['entries' => function($q) use ($startDate, $endDate) {
                    $q->whereHas('journal', fn($j) => $j->where('status', 'posted')->whereBetween('date', [$startDate, $endDate]));
                }], 'credit')
                ->withSum(['entries' => function($q) use ($startDate, $endDate) {
                    $q->whereHas('journal', fn($j) => $j->where('status', 'posted')->whereBetween('date', [$startDate, $endDate]));
                }], 'debit')
                ->get();

            $expenses = Account::where('company_id', $companyId)
                ->where('type', 'expense')
                ->withSum(['entries' => function($q) use ($startDate, $endDate) {
                    $q->whereHas('journal', fn($j) => $j->where('status', 'posted')->whereBetween('date', [$startDate, $endDate]));
                }], 'debit')
                ->withSum(['entries' => function($q) use ($startDate, $endDate) {
                    $q->whereHas('journal', fn($j) => $j->where('status', 'posted')->whereBetween('date', [$startDate, $endDate]));
                }], 'credit')
                ->get();

            $totalRevenue = $revenues->sum(fn($a) => $a->entries_sum_credit - $a->entries_sum_debit);
            $totalExpense = $expenses->sum(fn($a) => $a->entries_sum_debit - $a->entries_sum_credit);
            $netIncome = $totalRevenue - $totalExpense;

            if ($totalRevenue == 0 && $totalExpense == 0) {
                return back()->with('error', "Tidak ada transaksi Pendapatan atau Beban di tahun $year.");
            }

            $retainedEarnings = Account::where('company_id', $companyId)
                ->where('type', 'equity')
                ->where(function($q) {
                    $q->where('name', 'like', '%laba ditahan%')->orWhere('code', '303');
                })->first();

            if (!$retainedEarnings) {
                throw new \Exception('Akun Laba Ditahan (Retained Earnings) tidak ditemukan. Pastikan COA memiliki akun "Laba Ditahan".');
            }

            $journal = Journal::create([
                'company_id' => $companyId,
                'branch_id' => null,
                'date' => $endDate,
                'reference' => 'CLOSING-' . $year,
                'description' => 'Jurnal Penutup Tahun Buku ' . $year,
                'status' => 'posted',
                'created_by' => auth()->id(),
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            foreach ($revenues as $rev) {
                $balance = $rev->entries_sum_credit - $rev->entries_sum_debit;
                if ($balance != 0) {
                    $journal->entries()->create([
                        'account_id' => $rev->id,
                        'debit' => $balance,
                        'credit' => 0,
                        'description' => 'Menutup akun pendapatan ' . $rev->name,
                    ]);
                }
            }

            foreach ($expenses as $exp) {
                $balance = $exp->entries_sum_debit - $exp->entries_sum_credit;
                if ($balance != 0) {
                    $journal->entries()->create([
                        'account_id' => $exp->id,
                        'debit' => 0,
                        'credit' => $balance,
                        'description' => 'Menutup akun beban ' . $exp->name,
                    ]);
                }
            }

            if ($netIncome > 0) {
                $journal->entries()->create([
                    'account_id' => $retainedEarnings->id,
                    'debit' => 0,
                    'credit' => $netIncome,
                    'description' => 'Laba bersih tahun ' . $year,
                ]);
            } elseif ($netIncome < 0) {
                $journal->entries()->create([
                    'account_id' => $retainedEarnings->id,
                    'debit' => abs($netIncome),
                    'credit' => 0,
                    'description' => 'Rugi bersih tahun ' . $year,
                ]);
            }

            $company->update(['closed_until' => $endDate]);

            DB::commit();

            return redirect()->route('closing.index')->with('success', "Tutup buku tahun $year berhasil. Laba/Rugi telah dipindahkan ke Laba Ditahan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}