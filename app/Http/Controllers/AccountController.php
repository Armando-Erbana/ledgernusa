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

    // ============================
    // Import COA dari CSV
    // ============================

    public function importForm()
    {
        return view('accounts.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle); // baris pertama = header
        $companyId = session('company_id');
        $imported = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) continue;

            $code = trim($row[0]);
            $name = trim($row[1]);
            $type = strtolower(trim($row[2]));

            if (!in_array($type, ['asset', 'liability', 'equity', 'revenue', 'expense'])) {
                $errors[] = "Baris $code: tipe '$type' tidak valid.";
                continue;
            }

            try {
                Account::updateOrCreate(
                    ['company_id' => $companyId, 'code' => $code],
                    [
                        'name' => $name,
                        'type' => $type,
                        'normal_balance' => in_array($type, ['asset', 'expense']) ? 'debit' : 'credit',
                        'is_active' => true,
                    ]
                );
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris $code: " . $e->getMessage();
            }
        }

        fclose($handle);

        return redirect()->route('accounts.index')
            ->with('success', "$imported akun berhasil diimport.")
            ->with('import_errors', $errors);
    }
}