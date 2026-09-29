<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // Buku Besar per Akun
    public function ledger(Request $request)
    {
        $accounts = Account::orderBy('code')->get();
        $accountId = $request->get('account_id');
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $account = null;
        $entries = collect();
        $openingBalance = 0;
        $closingBalance = 0;

        if ($accountId) {
            $account = Account::findOrFail($accountId);

            // Saldo awal (sebelum $from)
            $openingDebit = JournalEntry::where('account_id', $accountId)
                ->whereHas('journal', function ($q) use ($from) {
                    $q->where('status', 'posted')->where('date', '<', $from);
                })->sum('debit');
            $openingCredit = JournalEntry::where('account_id', $accountId)
                ->whereHas('journal', function ($q) use ($from) {
                    $q->where('status', 'posted')->where('date', '<', $from);
                })->sum('credit');

            $openingBalance = $account->normal_balance === 'debit'
                ? ($openingDebit - $openingCredit)
                : ($openingCredit - $openingDebit);

            // Mutasi periode
            $entries = JournalEntry::with(['journal', 'account'])
                ->where('account_id', $accountId)
                ->whereHas('journal', function ($q) use ($from, $to) {
                    $q->where('status', 'posted')
                      ->whereBetween('date', [$from, $to]);
                })
                ->join('journals', 'journal_entries.journal_id', '=', 'journals.id')
                ->orderBy('journals.date')
                ->orderBy('journal_entries.id')
                ->select('journal_entries.*')
                ->get();

            // Saldo akhir
            $totalDebit = $entries->sum('debit');
            $totalCredit = $entries->sum('credit');
            $closingBalance = $account->normal_balance === 'debit'
                ? ($openingBalance + $totalDebit - $totalCredit)
                : ($openingBalance + $totalCredit - $totalDebit);
        }

        return view('reports.ledger', compact('accounts', 'account', 'entries', 'from', 'to', 'openingBalance', 'closingBalance'));
    }

    // Neraca Saldo (Trial Balance)
    public function trialBalance(Request $request)
    {
        $from = $request->get('from', now()->startOfYear()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $accounts = Account::orderBy('code')->get();
        $rows = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $acc) {
            $debit = JournalEntry::where('account_id', $acc->id)
                ->whereHas('journal', function ($q) use ($from, $to) {
                    $q->where('status', 'posted')->whereBetween('date', [$from, $to]);
                })->sum('debit');

            $credit = JournalEntry::where('account_id', $acc->id)
                ->whereHas('journal', function ($q) use ($from, $to) {
                    $q->where('status', 'posted')->whereBetween('date', [$from, $to]);
                })->sum('credit');

            $balance = $acc->normal_balance === 'debit' ? ($debit - $credit) : ($credit - $debit);

            if ($balance != 0 || $debit > 0 || $credit > 0) {
                $rows[] = [
                    'account' => $acc,
                    'debit' => $acc->normal_balance === 'debit' ? max($balance, 0) : 0,
                    'credit' => $acc->normal_balance === 'credit' ? max($balance, 0) : 0,
                    'balance' => $balance,
                ];
                $totalDebit += $acc->normal_balance === 'debit' ? max($balance, 0) : 0;
                $totalCredit += $acc->normal_balance === 'credit' ? max($balance, 0) : 0;
            }
        }

        return view('reports.trial-balance', compact('rows', 'from', 'to', 'totalDebit', 'totalCredit'));
    }

    // Laba Rugi
    public function incomeStatement(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $revenues = Account::where('type', 'revenue')->orderBy('code')->get()->map(function ($acc) use ($from, $to) {
            $debit = JournalEntry::where('account_id', $acc->id)
                ->whereHas('journal', fn($q) => $q->where('status', 'posted')->whereBetween('date', [$from, $to]))->sum('debit');
            $credit = JournalEntry::where('account_id', $acc->id)
                ->whereHas('journal', fn($q) => $q->where('status', 'posted')->whereBetween('date', [$from, $to]))->sum('credit');
            $acc->period_balance = $credit - $debit;
            return $acc;
        });

        $expenses = Account::where('type', 'expense')->orderBy('code')->get()->map(function ($acc) use ($from, $to) {
            $debit = JournalEntry::where('account_id', $acc->id)
                ->whereHas('journal', fn($q) => $q->where('status', 'posted')->whereBetween('date', [$from, $to]))->sum('debit');
            $credit = JournalEntry::where('account_id', $acc->id)
                ->whereHas('journal', fn($q) => $q->where('status', 'posted')->whereBetween('date', [$from, $to]))->sum('credit');
            $acc->period_balance = $debit - $credit;
            return $acc;
        });

        $totalRevenue = $revenues->sum('period_balance');
        $totalExpense = $expenses->sum('period_balance');
        $netIncome = $totalRevenue - $totalExpense;

        return view('reports.income-statement', compact('revenues', 'expenses', 'totalRevenue', 'totalExpense', 'netIncome', 'from', 'to'));
    }

    // Neraca
    public function balanceSheet(Request $request)
    {
        $asOf = $request->get('as_of', now()->format('Y-m-d'));

        $assets = Account::where('type', 'asset')->orderBy('code')->get()->map(function ($acc) use ($asOf) {
            $debit = JournalEntry::where('account_id', $acc->id)
                ->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('debit');
            $credit = JournalEntry::where('account_id', $acc->id)
                ->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('credit');
            $acc->balance = $debit - $credit;
            return $acc;
        });

        $liabilities = Account::where('type', 'liability')->orderBy('code')->get()->map(function ($acc) use ($asOf) {
            $debit = JournalEntry::where('account_id', $acc->id)
                ->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('debit');
            $credit = JournalEntry::where('account_id', $acc->id)
                ->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('credit');
            $acc->balance = $credit - $debit;
            return $acc;
        });

        $equities = Account::where('type', 'equity')->orderBy('code')->get()->map(function ($acc) use ($asOf) {
            $debit = JournalEntry::where('account_id', $acc->id)
                ->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('debit');
            $credit = JournalEntry::where('account_id', $acc->id)
                ->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('credit');
            $acc->balance = $credit - $debit;
            return $acc;
        });

        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equities->sum('balance');

        // Laba berjalan
        $revenueTotal = Account::where('type', 'revenue')->get()->sum(function ($acc) use ($asOf) {
            $d = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('debit');
            $c = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('credit');
            return $c - $d;
        });
        $expenseTotal = Account::where('type', 'expense')->get()->sum(function ($acc) use ($asOf) {
            $d = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('debit');
            $c = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('credit');
            return $d - $c;
        });
        $currentEarnings = $revenueTotal - $expenseTotal;

        return view('reports.balance-sheet', compact('assets','liabilities','equities','totalAssets','totalLiabilities','totalEquity','currentEarnings','asOf'));
    }
}