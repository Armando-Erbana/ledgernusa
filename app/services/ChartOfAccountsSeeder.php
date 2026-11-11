<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Company;

class ChartOfAccountsSeeder
{
    public function seed(Company $company, string $businessType): void
    {
        $accounts = $businessType === 'jasa'
            ? $this->jasaAccounts()
            : $this->dagangAccounts();

        foreach ($accounts as $a) {
            Account::updateOrCreate(
                ['company_id' => $company->id, 'code' => $a['code']],
                array_merge($a, [
                    'company_id' => $company->id,
                    'is_active'  => true,
                ])
            );
        }
    }

    private function jasaAccounts(): array
    {
        return [
            ['code' => '101', 'name' => 'Kas',                      'type' => 'asset', 'normal_balance' => 'debit', 'is_cash' => true],
            ['code' => '102', 'name' => 'Bank',                     'type' => 'asset', 'normal_balance' => 'debit', 'is_bank' => true],
            ['code' => '103', 'name' => 'Piutang Usaha',            'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '104', 'name' => 'Piutang Lain-lain',        'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '105', 'name' => 'Perlengkapan',             'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '121', 'name' => 'Peralatan',                'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '129', 'name' => 'Akumulasi Penyusutan',     'type' => 'asset', 'normal_balance' => 'credit'],
            ['code' => '201', 'name' => 'Utang Usaha',              'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '211', 'name' => 'PPN Keluaran',             'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '212', 'name' => 'PPN Masukan',              'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '213', 'name' => 'Utang PPh 21',             'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '301', 'name' => 'Modal Pemilik',            'type' => 'equity', 'normal_balance' => 'credit'],
            ['code' => '302', 'name' => 'Prive',                    'type' => 'equity', 'normal_balance' => 'debit'],
            ['code' => '303', 'name' => 'Laba Ditahan',             'type' => 'equity', 'normal_balance' => 'credit'],
            ['code' => '401', 'name' => 'Pendapatan Jasa',          'type' => 'revenue', 'normal_balance' => 'credit'],
            ['code' => '402', 'name' => 'Pendapatan Konsultasi',    'type' => 'revenue', 'normal_balance' => 'credit'],
            ['code' => '403', 'name' => 'Pendapatan Lain-lain',     'type' => 'revenue', 'normal_balance' => 'credit'],
            ['code' => '501', 'name' => 'Beban Gaji',               'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '502', 'name' => 'Beban Sewa',               'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '503', 'name' => 'Beban Listrik & Air',      'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '504', 'name' => 'Beban Internet & Telepon', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '505', 'name' => 'Beban Perlengkapan',       'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '511', 'name' => 'Beban Penyusutan',         'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '599', 'name' => 'Beban Lain-lain',          'type' => 'expense', 'normal_balance' => 'debit'],
        ];
    }

    private function dagangAccounts(): array
    {
        return [
            ['code' => '101', 'name' => 'Kas',                      'type' => 'asset', 'normal_balance' => 'debit', 'is_cash' => true],
            ['code' => '102', 'name' => 'Bank',                     'type' => 'asset', 'normal_balance' => 'debit', 'is_bank' => true],
            ['code' => '103', 'name' => 'Piutang Usaha',            'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '104', 'name' => 'Piutang Lain-lain',        'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '105', 'name' => 'Persediaan Barang Dagang', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '121', 'name' => 'Peralatan',                'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '129', 'name' => 'Akumulasi Penyusutan',     'type' => 'asset', 'normal_balance' => 'credit'],
            ['code' => '201', 'name' => 'Utang Usaha',              'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '211', 'name' => 'PPN Keluaran',             'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '212', 'name' => 'PPN Masukan',              'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '213', 'name' => 'Utang PPh 21',             'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '301', 'name' => 'Modal Pemilik',            'type' => 'equity', 'normal_balance' => 'credit'],
            ['code' => '302', 'name' => 'Prive',                    'type' => 'equity', 'normal_balance' => 'debit'],
            ['code' => '303', 'name' => 'Laba Ditahan',             'type' => 'equity', 'normal_balance' => 'credit'],
            ['code' => '401', 'name' => 'Penjualan',                'type' => 'revenue', 'normal_balance' => 'credit'],
            ['code' => '402', 'name' => 'Retur Penjualan',          'type' => 'revenue', 'normal_balance' => 'debit'],
            ['code' => '403', 'name' => 'Diskon Penjualan',         'type' => 'revenue', 'normal_balance' => 'debit'],
            ['code' => '409', 'name' => 'Pendapatan Lain-lain',     'type' => 'revenue', 'normal_balance' => 'credit'],
            ['code' => '501', 'name' => 'Harga Pokok Penjualan',    'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '502', 'name' => 'Pembelian',                'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '503', 'name' => 'Beban Angkut Pembelian',   'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '511', 'name' => 'Beban Gaji',               'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '512', 'name' => 'Beban Sewa',               'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '513', 'name' => 'Beban Listrik & Air',      'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '514', 'name' => 'Beban Internet & Telepon', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '521', 'name' => 'Beban Penyusutan',         'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '599', 'name' => 'Beban Lain-lain',          'type' => 'expense', 'normal_balance' => 'debit'],
        ];
    }
}