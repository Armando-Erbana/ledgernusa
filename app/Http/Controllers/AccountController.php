<?php
namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $companyId = session('company_id');
        $accounts = Account::where('company_id', $companyId)
            ->orderBy('code')->get();
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        $companyId = session('company_id');
        $parents = Account::where('company_id', $companyId)->orderBy('code')->get();
        return view('accounts.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $companyId = session('company_id');

        $data = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:accounts,id',
            'is_cash' => 'nullable|boolean',
            'is_bank' => 'nullable|boolean',
        ]);

        $data['company_id'] = $companyId;
        $data['normal_balance'] = in_array($data['type'], ['asset', 'expense']) ? 'debit' : 'credit';
        $data['is_cash'] = $request->boolean('is_cash');
        $data['is_bank'] = $request->boolean('is_bank');

        Account::create($data);

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil ditambahkan');
    }

    public function edit(Account $account)
    {
        $companyId = session('company_id');
        $parents = Account::where('company_id', $companyId)
            ->where('id', '!=', $account->id)->orderBy('code')->get();
        return view('accounts.edit', compact('account', 'parents'));
    }

    public function update(Request $request, Account $account)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:accounts,id',
            'is_active' => 'nullable|boolean',
            'is_cash' => 'nullable|boolean',
            'is_bank' => 'nullable|boolean',
        ]);

        $data['normal_balance'] = in_array($data['type'], ['asset', 'expense']) ? 'debit' : 'credit';
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_cash'] = $request->boolean('is_cash');
        $data['is_bank'] = $request->boolean('is_bank');

        $account->update($data);

        return redirect()->route('accounts.index')->with('success', 'Akun diperbarui');
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Akun dihapus');
    }
}