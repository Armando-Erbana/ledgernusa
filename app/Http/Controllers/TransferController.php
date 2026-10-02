<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $query = Transfer::with(['fromAccount', 'toAccount'])->latest('date')->latest('id');
        $transfers = $query->paginate(20)->withQueryString();
        return view('transfers.index', compact('transfers'));
    }

    public function create()
    {
        $accounts = Account::where(function ($q) {
                $q->where('is_cash', true)->orWhere('is_bank', true);
            })
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('transfers.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'nullable|string|max:100',
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
            'admin_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        $data['company_id'] = session('company_id');
        $data['created_by'] = auth()->id();
        $data['admin_fee'] = $data['admin_fee'] ?? 0;

        DB::transaction(function () use ($data) {
            $transfer = Transfer::create($data);
            $journal = $transfer->generateJournal();
            $transfer->update(['journal_id' => $journal->id]);
        });

        return redirect()->route('transfers.index')->with('success', 'Transfer berhasil dicatat.');
    }

    public function show(Transfer $transfer)
    {
        $transfer->load(['fromAccount', 'toAccount', 'journal.entries.account']);
        return view('transfers.show', compact('transfer'));
    }

    public function edit(Transfer $transfer)
    {
        $accounts = Account::where(function ($q) {
                $q->where('is_cash', true)->orWhere('is_bank', true);
            })
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('transfers.edit', compact('transfer', 'accounts'));
    }

    public function update(Request $request, Transfer $transfer)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'nullable|string|max:100',
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
            'admin_fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        $data['admin_fee'] = $data['admin_fee'] ?? 0;

        DB::transaction(function () use ($transfer, $data) {
            if ($transfer->journal) {
                $transfer->journal->entries()->delete();
                $transfer->journal->delete();
            }
            $transfer->update($data);
            $journal = $transfer->generateJournal();
            $transfer->update(['journal_id' => $journal->id]);
        });

        return redirect()->route('transfers.index')->with('success', 'Transfer diperbarui.');
    }

    public function destroy(Transfer $transfer)
    {
        DB::transaction(function () use ($transfer) {
            if ($transfer->journal) {
                $transfer->journal->entries()->delete();
                $transfer->journal->delete();
            }
            $transfer->delete();
        });
        return redirect()->route('transfers.index')->with('success', 'Transfer dihapus.');
    }
}