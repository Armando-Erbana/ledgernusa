<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    public function index()
    {
        $journals = Journal::where('company_id', session('company_id'))
            ->with('entries')
            ->latest('date')->paginate(20);
        return view('journals.index', compact('journals'));
    }

    public function create()
    {
        $accounts = Account::where('company_id', session('company_id'))
            ->where('is_active', true)
            ->orderBy('code')->get();
        return view('journals.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'nullable|string',
            'description' => 'nullable|string',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:accounts,id',
            'entries.*.debit' => 'nullable|numeric|min:0',
            'entries.*.credit' => 'nullable|numeric|min:0',
        ]);

        $totalDebit = collect($data['entries'])->sum('debit');
        $totalCredit = collect($data['entries'])->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withInput()->withErrors(['balance' => 'Total debit & kredit harus sama.']);
        }

        DB::transaction(function () use ($data) {
            $journal = Journal::create([
                'company_id' => session('company_id'),
                'date' => $data['date'],
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($data['entries'] as $entry) {
                if (($entry['debit'] ?? 0) == 0 && ($entry['credit'] ?? 0) == 0) continue;
                $journal->entries()->create([
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                ]);
            }
        });

        return redirect()->route('journals.index')->with('success', 'Jurnal berhasil disimpan sebagai draft');
    }

    public function show(Journal $journal)
    {
        $journal->load('entries.account');
        return view('journals.show', compact('journal'));
    }

    public function edit(Journal $journal)
    {
        if ($journal->status === 'posted') abort(403);
        $accounts = Account::where('company_id', session('company_id'))
            ->where('is_active', true)->orderBy('code')->get();
        $journal->load('entries');
        return view('journals.edit', compact('journal', 'accounts'));
    }

    public function update(Request $request, Journal $journal)
    {
        if ($journal->status === 'posted') abort(403);

        $data = $request->validate([
            'date' => 'required|date',
            'reference' => 'nullable|string',
            'description' => 'nullable|string',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:accounts,id',
            'entries.*.debit' => 'nullable|numeric|min:0',
            'entries.*.credit' => 'nullable|numeric|min:0',
        ]);

        $totalDebit = collect($data['entries'])->sum('debit');
        $totalCredit = collect($data['entries'])->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withInput()->withErrors(['balance' => 'Total debit & kredit harus sama.']);
        }

        DB::transaction(function () use ($journal, $data) {
            $journal->update([
                'date' => $data['date'],
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            $journal->entries()->delete();
            foreach ($data['entries'] as $entry) {
                if (($entry['debit'] ?? 0) == 0 && ($entry['credit'] ?? 0) == 0) continue;
                $journal->entries()->create([
                    'account_id' => $entry['account_id'],
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                ]);
            }
        });

        return redirect()->route('journals.index')->with('success', 'Jurnal diperbarui');
    }

    public function destroy(Journal $journal)
    {
        if ($journal->status === 'posted') abort(403);
        $journal->delete();
        return redirect()->route('journals.index')->with('success', 'Jurnal dihapus');
    }

    public function post(Journal $journal)
    {
        if ($journal->status === 'posted') {
            return back()->with('error', 'Jurnal sudah di-posting.');
        }

        $journal->update([
            'status' => 'posted',
            'posted_by' => auth()->id(),
            'posted_at' => now(),
        ]);

        return back()->with('success', 'Jurnal berhasil diposting.');
    }
}