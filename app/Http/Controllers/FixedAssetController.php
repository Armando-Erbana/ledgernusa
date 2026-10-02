<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\FixedAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FixedAssetController extends Controller
{
    public function index(Request $request)
    {
        $query = FixedAsset::orderBy('status')->orderBy('name');

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $assets = $query->paginate(20)->withQueryString();

        $totalCost = FixedAsset::sum('purchase_cost');
        $totalAccum = FixedAsset::sum('accumulated_depreciation');
        $totalBook = FixedAsset::sum('book_value');

        return view('fixed-assets.index', compact('assets', 'totalCost', 'totalAccum', 'totalBook'));
    }

    public function create()
    {
        $companyId = session('company_id');

        $assetAccounts = Account::where('company_id', $companyId)
            ->where('type', 'asset')
            ->where('is_cash', false)
            ->where('is_bank', false)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $depreciationAccounts = Account::where('company_id', $companyId)
            ->where('type', 'asset')
            ->where('name', 'like', '%akumulasi%')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        if ($depreciationAccounts->isEmpty()) {
            $depreciationAccounts = $assetAccounts;
        }

        $expenseAccounts = Account::where('company_id', $companyId)
            ->where('type', 'expense')
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('fixed-assets.create', compact('assetAccounts', 'depreciationAccounts', 'expenseAccounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'nullable|string|max:30',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'asset_account_id' => 'required|exists:accounts,id',
            'depreciation_account_id' => 'required|exists:accounts,id',
            'expense_account_id' => 'required|exists:accounts,id',
            'purchase_date' => 'required|date',
            'purchase_cost' => 'required|numeric|min:0.01',
            'residual_value' => 'nullable|numeric|min:0',
            'useful_life_months' => 'required|integer|min:1|max:600',
            'depreciation_method' => 'required|in:straight_line,double_declining,sum_of_years',
        ]);

        $data['company_id'] = session('company_id');
        $data['created_by'] = auth()->id();
        $data['residual_value'] = $data['residual_value'] ?? 0;
        $data['book_value'] = $data['purchase_cost'];
        $data['accumulated_depreciation'] = 0;

        DB::transaction(function () use ($data) {
            $asset = FixedAsset::create($data);
            $journal = $asset->generateAcquisitionJournal();
            $asset->update(['journal_id' => $journal->id]);
        });

        return redirect()->route('fixed-assets.index')->with('success', 'Aset tetap berhasil ditambahkan & jurnal perolehan dibuat.');
    }

    public function show(FixedAsset $fixedAsset)
    {
        $fixedAsset->load(['assetAccount', 'depreciationAccount', 'expenseAccount', 'journal.entries.account', 'depreciations.journal']);
        return view('fixed-assets.show', ['asset' => $fixedAsset]);
    }

    public function edit(FixedAsset $fixedAsset)
    {
        $companyId = session('company_id');

        $assetAccounts = Account::where('company_id', $companyId)
            ->where('type', 'asset')->where('is_cash', false)->where('is_bank', false)
            ->orderBy('code')->get();

        $depreciationAccounts = Account::where('company_id', $companyId)
            ->where('type', 'asset')->where('name', 'like', '%akumulasi%')
            ->orderBy('code')->get();

        if ($depreciationAccounts->isEmpty()) $depreciationAccounts = $assetAccounts;

        $expenseAccounts = Account::where('company_id', $companyId)
            ->where('type', 'expense')->orderBy('code')->get();

        return view('fixed-assets.edit', [
            'asset' => $fixedAsset,
            'assetAccounts' => $assetAccounts,
            'depreciationAccounts' => $depreciationAccounts,
            'expenseAccounts' => $expenseAccounts,
        ]);
    }

    public function update(Request $request, FixedAsset $fixedAsset)
    {
        $data = $request->validate([
            'code' => 'nullable|string|max:30',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'asset_account_id' => 'required|exists:accounts,id',
            'depreciation_account_id' => 'required|exists:accounts,id',
            'expense_account_id' => 'required|exists:accounts,id',
            'purchase_date' => 'required|date',
            'purchase_cost' => 'required|numeric|min:0.01',
            'residual_value' => 'nullable|numeric|min:0',
            'useful_life_months' => 'required|integer|min:1|max:600',
            'depreciation_method' => 'required|in:straight_line,double_declining,sum_of_years',
        ]);

        if ($fixedAsset->depreciations()->exists() && $data['purchase_cost'] != $fixedAsset->purchase_cost) {
            return back()->with('error', 'Tidak bisa mengubah harga perolehan karena sudah ada depresiasi.');
        }

        $data['residual_value'] = $data['residual_value'] ?? 0;
        $fixedAsset->update($data);

        return redirect()->route('fixed-assets.show', $fixedAsset)->with('success', 'Aset diperbarui.');
    }

    public function destroy(FixedAsset $fixedAsset)
    {
        if ($fixedAsset->depreciations()->exists()) {
            return back()->with('error', 'Aset sudah memiliki depresiasi. Lepas (dispose) saja, jangan dihapus.');
        }
        DB::transaction(function () use ($fixedAsset) {
            if ($fixedAsset->journal) {
                $fixedAsset->journal->entries()->delete();
                $fixedAsset->journal->delete();
            }
            $fixedAsset->delete();
        });
        return redirect()->route('fixed-assets.index')->with('success', 'Aset dihapus.');
    }

    public function dispose(Request $request, FixedAsset $fixedAsset)
    {
        $data = $request->validate([
            'disposed_at' => 'required|date',
            'disposal_value' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($fixedAsset, $data) {
            $journal = \App\Models\Journal::create([
                'company_id' => $fixedAsset->company_id,
                'date' => $data['disposed_at'],
                'reference' => 'DSP-' . ($fixedAsset->code ?: $fixedAsset->id),
                'description' => 'Pelepasan aset: ' . $fixedAsset->name,
                'status' => 'posted',
                'created_by' => auth()->id(),
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            $cashAccount = Account::where('company_id', $fixedAsset->company_id)
                ->where(function ($q) {
                    $q->where('is_cash', true)->orWhere('is_bank', true);
                })->orderBy('code')->first();

            $bookValue = (float) $fixedAsset->book_value;
            $disposalValue = (float) $data['disposal_value'];
            $gain = $disposalValue - $bookValue;

            $entries = [
                ['account_id' => $cashAccount?->id, 'debit' => $disposalValue, 'credit' => 0, 'description' => 'Kas dari pelepasan aset'],
                ['account_id' => $fixedAsset->depreciation_account_id, 'debit' => $fixedAsset->accumulated_depreciation, 'credit' => 0, 'description' => 'Hapus akumulasi'],
                ['account_id' => $fixedAsset->asset_account_id, 'debit' => 0, 'credit' => $fixedAsset->purchase_cost, 'description' => 'Hapus aset'],
            ];

            if ($gain != 0) {
                $gainAccount = Account::where('company_id', $fixedAsset->company_id)
                    ->where('type', $gain > 0 ? 'revenue' : 'expense')
                    ->orderBy('code')->first();

                if ($gainAccount) {
                    $entries[] = [
                        'account_id' => $gainAccount->id,
                        'debit' => $gain < 0 ? abs($gain) : 0,
                        'credit' => $gain > 0 ? $gain : 0,
                        'description' => $gain > 0 ? 'Laba pelepasan aset' : 'Rugi pelepasan aset',
                    ];
                }
            }

            $journal->entries()->createMany($entries);

            $fixedAsset->update([
                'status' => 'disposed',
                'disposed_at' => $data['disposed_at'],
                'disposal_value' => $data['disposal_value'],
            ]);
        });

        return redirect()->route('fixed-assets.index')->with('success', 'Aset dilepas.');
    }
}