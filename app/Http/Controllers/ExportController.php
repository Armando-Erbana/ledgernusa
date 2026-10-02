<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Purchase;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function incomeStatementPdf(Request $request)
    {
        $data = $this->getIncomeStatementData($request);
        $pdf = Pdf::loadView('exports.income-statement', $data)->setPaper('a4');
        return $pdf->download('laba-rugi-' . now()->format('Ymd') . '.pdf');
    }

    public function balanceSheetPdf(Request $request)
    {
        $data = $this->getBalanceSheetData($request);
        $pdf = Pdf::loadView('exports.balance-sheet', $data)->setPaper('a4');
        return $pdf->download('neraca-' . now()->format('Ymd') . '.pdf');
    }

    public function trialBalancePdf(Request $request)
    {
        $data = $this->getTrialBalanceData($request);
        $pdf = Pdf::loadView('exports.trial-balance', $data)->setPaper('a4');
        return $pdf->download('neraca-saldo-' . now()->format('Ymd') . '.pdf');
    }

    public function salesExcel(Request $request)
    {
        $companyId = session('company_id');
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $sales = Sale::where('company_id', $companyId)
            ->whereBetween('date', [$from, $to])
            ->with('customer')
            ->orderBy('date')
            ->get();

        $filename = 'penjualan-' . $from . '-sd-' . $to . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($sales) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No Invoice', 'Tanggal', 'Customer', 'DPP', 'PPN', 'Total', 'Dibayar', 'Sisa', 'Status']);
            foreach ($sales as $s) {
                fputcsv($file, [
                    $s->invoice_number,
                    $s->date->format('Y-m-d'),
                    $s->customer->name ?? '-',
                    $s->subtotal - $s->discount,
                    $s->tax,
                    $s->total,
                    $s->paid_amount,
                    $s->balance,
                    $s->status,
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function purchasesExcel(Request $request)
    {
        $companyId = session('company_id');
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $purchases = Purchase::where('company_id', $companyId)
            ->whereBetween('date', [$from, $to])
            ->with('supplier')
            ->orderBy('date')
            ->get();

        $filename = 'pembelian-' . $from . '-sd-' . $to . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($purchases) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No Bill', 'Tanggal', 'Supplier', 'DPP', 'PPN', 'Total', 'Dibayar', 'Sisa', 'Status']);
            foreach ($purchases as $p) {
                fputcsv($file, [
                    $p->bill_number,
                    $p->date->format('Y-m-d'),
                    $p->supplier->name ?? '-',
                    $p->subtotal - $p->discount,
                    $p->tax,
                    $p->total,
                    $p->paid_amount,
                    $p->balance,
                    $p->status,
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // ============ Helper: ambil data laporan ============

    private function getIncomeStatementData(Request $request): array
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $revenues = Account::where('type', 'revenue')->orderBy('code')->get()->map(function ($acc) use ($from, $to) {
            $debit = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->whereBetween('date', [$from, $to]))->sum('debit');
            $credit = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->whereBetween('date', [$from, $to]))->sum('credit');
            $acc->period_balance = $credit - $debit;
            return $acc;
        });
        $expenses = Account::where('type', 'expense')->orderBy('code')->get()->map(function ($acc) use ($from, $to) {
            $debit = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->whereBetween('date', [$from, $to]))->sum('debit');
            $credit = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->whereBetween('date', [$from, $to]))->sum('credit');
            $acc->period_balance = $debit - $credit;
            return $acc;
        });

        $totalRevenue = $revenues->sum('period_balance');
        $totalExpense = $expenses->sum('period_balance');
        $netIncome = $totalRevenue - $totalExpense;

        $company = \App\Models\Company::find(session('company_id'));

        return compact('revenues', 'expenses', 'totalRevenue', 'totalExpense', 'netIncome', 'from', 'to', 'company');
    }

    private function getBalanceSheetData(Request $request): array
    {
        $asOf = $request->get('as_of', now()->format('Y-m-d'));
        $assets = Account::where('type', 'asset')->orderBy('code')->get()->map(function ($acc) use ($asOf) {
            $debit = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('debit');
            $credit = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('credit');
            $acc->balance = $debit - $credit;
            return $acc;
        });
        $liabilities = Account::where('type', 'liability')->orderBy('code')->get()->map(function ($acc) use ($asOf) {
            $debit = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('debit');
            $credit = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('credit');
            $acc->balance = $credit - $debit;
            return $acc;
        });
        $equities = Account::where('type', 'equity')->orderBy('code')->get()->map(function ($acc) use ($asOf) {
            $debit = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('debit');
            $credit = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->where('date', '<=', $asOf))->sum('credit');
            $acc->balance = $credit - $debit;
            return $acc;
        });

        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equities->sum('balance');

        $company = \App\Models\Company::find(session('company_id'));

        return compact('assets', 'liabilities', 'equities', 'totalAssets', 'totalLiabilities', 'totalEquity', 'asOf', 'company');
    }

    private function getTrialBalanceData(Request $request): array
    {
        $from = $request->get('from', now()->startOfYear()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));
        $accounts = Account::orderBy('code')->get();
        $rows = [];
        $totalDebit = 0; $totalCredit = 0;

        foreach ($accounts as $acc) {
            $debit = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->whereBetween('date', [$from, $to]))->sum('debit');
            $credit = JournalEntry::where('account_id', $acc->id)->whereHas('journal', fn($q) => $q->where('status', 'posted')->whereBetween('date', [$from, $to]))->sum('credit');
            $balance = $acc->normal_balance === 'debit' ? ($debit - $credit) : ($credit - $debit);

            if ($balance != 0 || $debit > 0 || $credit > 0) {
                $rows[] = [
                    'account' => $acc,
                    'debit' => $acc->normal_balance === 'debit' ? max($balance, 0) : 0,
                    'credit' => $acc->normal_balance === 'credit' ? max($balance, 0) : 0,
                ];
                $totalDebit += $acc->normal_balance === 'debit' ? max($balance, 0) : 0;
                $totalCredit += $acc->normal_balance === 'credit' ? max($balance, 0) : 0;
            }
        }

        $company = \App\Models\Company::find(session('company_id'));
        return compact('rows', 'from', 'to', 'totalDebit', 'totalCredit', 'company');
    }
}