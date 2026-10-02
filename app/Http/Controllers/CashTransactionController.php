<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CashTransaction;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = CashTransaction::with(['cashAccount', 'counterAccount', 'contact'])
            ->latest('date')
            ->latest('id');

        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->filled('from')) {
            $query->where('date', '>=', $request->get('from'));
        }

        if ($request->filled('to')) {
            $query->where('date', '<=', $request->get('to'));
        }

        $transactions = $query->paginate(20)->withQueryString();

        $totalIn = CashTransaction::where('type', 'in')
            ->when($request->filled('from'), fn($q) => $q->where('date', '>=', $request->get('from')))
            ->when($request->filled('to'), fn($q) => $q->where('date', '<=', $request->get('to')))
            ->sum('amount');

        $totalOut = CashTransaction::where('type', 'out')
            ->when($request->filled('from'), fn($q) => $q->where('date', '>=', $request->get('from')))
            ->when($request->filled('to'), fn($q) => $q->where('date', '<=', $request->get('to')))
            ->sum('amount');

        return view('cash.index', compact('transactions', 'totalIn', 'totalOut'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'in');

        $cashAccounts = Account::where(function ($q) {
                $q->where('is_cash', true)->orWhere('is_bank', true);
            })
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $allAccounts = Account::where('is_active', true)
            ->whereNotIn('id', $cashAccounts->pluck('id'))
            ->orderBy('code')
            ->get();

        $contacts = Contact::orderBy('name')->get();

        return view('cash.create', compact('type', 'cashAccounts', 'allAccounts', 'contacts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:in,out',
            'date' => 'required|date',
            'reference' => 'nullable|string|max:100',
            'cash_account_id' => 'required|exists:accounts,id',
            'counter_account_id' => 'required|exists:accounts,id|different:cash_account_id',
            'contact_id' => 'nullable|exists:contacts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
        ]);

        $data['company_id'] = session('company_id');
        $data['created_by'] = auth()->id();
        $data['status'] = 'posted';

        DB::transaction(function () use ($data) {
            $transaction = CashTransaction::create($data);
            $journal = $transaction->generateJournal();
            $transaction->update(['journal_id' => $journal->id]);
        });

        $typeLabel = $data['type'] === 'in' ? 'Kas Masuk' : 'Kas Keluar';

        return redirect()->route('cash.index')
            ->with('success', "{$typeLabel} berhasil dicatat & jurnal otomatis dibuat.");
    }

    public function show(CashTransaction $cash)
    {
        $cash->load(['cashAccount', 'counterAccount', 'contact', 'journal.entries.account']);
        return view('cash.show', compact('cash'));
    }

    public function edit(CashTransaction $cash)
    {
        $cashAccounts = Account::where(function ($q) {
                $q->where('is_cash', true)->orWhere('is_bank', true);
            })
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $allAccounts = Account::where('is_active', true)
            ->whereNotIn('id', $cashAccounts->pluck('id'))
            ->orderBy('code')
            ->get();

        $contacts = Contact::orderBy('name')->get();

        return view('cash.edit', compact('cash', 'cashAccounts', 'allAccounts', 'contacts'));
    }

    public function update(Request $request, CashTransaction $cash)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'nullable|string|max:100',
            'cash_account_id' => 'required|exists:accounts,id',
            'counter_account_id' => 'required|exists:accounts,id|different:cash_account_id',
            'contact_id' => 'nullable|exists:contacts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($cash, $data) {
            // Hapus jurnal lama
            if ($cash->journal) {
                $cash->journal->entries()->delete();
                $cash->journal->delete();
            }

            // Update cash transaction
            $cash->update($data);

            // Generate ulang jurnal
            $journal = $cash->generateJournal();
            $cash->update(['journal_id' => $journal->id]);
        });

        return redirect()->route('cash.index')->with('success', 'Transaksi kas diperbarui.');
    }

    public function destroy(CashTransaction $cash)
    {
        DB::transaction(function () use ($cash) {
            if ($cash->journal) {
                $cash->journal->entries()->delete();
                $cash->journal->delete();
            }
            $cash->delete();
        });

        return redirect()->route('cash.index')->with('success', 'Transaksi kas dihapus.');
    }
}