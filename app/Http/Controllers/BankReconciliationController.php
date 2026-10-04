<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\BankStatement;
use App\Models\BankStatementLine;
use App\Services\BankReconciliationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankReconciliationController extends Controller
{
    public function index()
    {
        $statements = BankStatement::with('account')
            ->orderBy('period_end', 'desc')
            ->paginate(20);
        return view('bank-rec.index', compact('statements'));
    }

    public function create()
    {
        $bankAccounts = Account::where('company_id', session('company_id'))
            ->where('is_bank', true)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
        return view('bank-rec.create', compact('bankAccounts'));
    }

    public function store(Request $request, BankReconciliationService $service)
    {
        $data = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $rows = $service->parseCsv($request->file('file'));
        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau format salah.');
        }

        $dates = collect($rows)->pluck('date')->sort();
        $periodStart = $dates->first();
        $periodEnd = $dates->last();

        $opening = $rows[0]['balance'] ?? 0;
        $lastRow = end($rows);
        $closing = $lastRow['balance'] ?? 0;

        // Ambil saldo dari data sistem untuk referensi
        $account = Account::find($data['account_id']);
        $systemBalance = $account->balance;

        DB::transaction(function () use ($data, $rows, $periodStart, $periodEnd, $opening, $closing, $request) {
            $statement = BankStatement::create([
                'company_id' => session('company_id'),
                'account_id' => $data['account_id'],
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'file_name' => $request->file('file')->getClientOriginalName(),
                'opening_balance' => $opening,
                'closing_balance' => $closing,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($rows as $r) {
                BankStatementLine::create([
                    'bank_statement_id' => $statement->id,
                    'company_id' => session('company_id'),
                    'date' => $r['date'],
                    'description' => $r['description'],
                    'reference' => $r['reference'],
                    'debit' => $r['debit'],
                    'credit' => $r['credit'],
                    'balance' => $r['balance'],
                ]);
            }
        });

        return redirect()->route('bank-rec.show', $statement ?? BankStatement::latest()->first())
            ->with('success', 'Mutasi bank berhasil diimport. ' . count($rows) . ' baris.');
    }

    public function show(BankStatement $bankRec)
    {
        $bankRec->load('account', 'lines');

        // Saldo sistem (dari jurnal) untuk akun bank ini
        $systemBalance = $bankRec->account->balance;

        // Saldo bank statement
        $statementBalance = $bankRec->closing_balance;

        $matched = $bankRec->lines->where('match_status', 'matched');
        $unmatched = $bankRec->lines->where('match_status', 'unmatched');

        return view('bank-rec.show', compact('bankRec', 'systemBalance', 'statementBalance', 'matched', 'unmatched'));
    }

    public function autoMatch(BankStatement $bankRec, BankReconciliationService $service)
    {
        $result = $service->autoMatch($bankRec);
        return back()->with('success', "Auto-match selesai: {$result['matched']} cocok, {$result['unmatched']} belum.");
    }

    public function matchLine(Request $request, BankStatement $bankRec, BankStatementLine $line)
    {
        $data = $request->validate([
            'matched_type' => 'required|in:cash_transaction,sale_payment,purchase_payment',
            'matched_id' => 'required|integer',
        ]);

        $line->update([
            'match_status' => 'matched',
            'matched_type' => $data['matched_type'],
            'matched_id' => $data['matched_id'],
            'matched_at' => now(),
            'matched_by' => auth()->id(),
        ]);

        return back()->with('success', 'Baris berhasil dicocokkan.');
    }

    public function unmatchLine(BankStatement $bankRec, BankStatementLine $line)
    {
        $line->update([
            'match_status' => 'unmatched',
            'matched_type' => null,
            'matched_id' => null,
            'matched_at' => null,
            'matched_by' => null,
        ]);
        return back()->with('success', 'Match dibatalkan.');
    }

    public function excludeLine(BankStatement $bankRec, BankStatementLine $line)
    {
        $line->update(['match_status' => 'excluded']);
        return back()->with('success', 'Baris dikecualikan.');
    }

    public function finalize(BankStatement $bankRec)
    {
        $bankRec->update(['status' => 'reconciled']);
        return back()->with('success', 'Rekonsiliasi selesai.');
    }

    public function destroy(BankStatement $bankRec)
    {
        $bankRec->lines()->delete();
        $bankRec->delete();
        return redirect()->route('bank-rec.index')->with('success', 'Rekonsiliasi dihapus.');
    }
}