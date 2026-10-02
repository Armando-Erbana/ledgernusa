<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Company;
use Illuminate\Database\Seeder;

class AssetAccountsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Company::all() as $company) {
            $accounts = [
                // Aset tetap
                ['code' => '121', 'name' => 'Kendaraan', 'type' => 'asset', 'normal_balance' => 'debit'],
                ['code' => '122', 'name' => 'Peralatan Kantor', 'type' => 'asset', 'normal_balance' => 'debit'],
                ['code' => '123', 'name' => 'Komputer & Elektronik', 'type' => 'asset', 'normal_balance' => 'debit'],
                ['code' => '124', 'name' => 'Furniture', 'type' => 'asset', 'normal_balance' => 'debit'],
                ['code' => '125', 'name' => 'Bangunan', 'type' => 'asset', 'normal_balance' => 'debit'],

                // Akumulasi depresiasi
                ['code' => '129', 'name' => 'Akumulasi Depresiasi Kendaraan', 'type' => 'asset', 'normal_balance' => 'credit'],
                ['code' => '130', 'name' => 'Akumulasi Depresiasi Peralatan', 'type' => 'asset', 'normal_balance' => 'credit'],
                ['code' => '131', 'name' => 'Akumulasi Depresiasi Komputer', 'type' => 'asset', 'normal_balance' => 'credit'],
                ['code' => '132', 'name' => 'Akumulasi Depresiasi Furniture', 'type' => 'asset', 'normal_balance' => 'credit'],
                ['code' => '133', 'name' => 'Akumulasi Depresiasi Bangunan', 'type' => 'asset', 'normal_balance' => 'credit'],

                // Beban depresiasi
                ['code' => '511', 'name' => 'Beban Depresiasi Kendaraan', 'type' => 'expense', 'normal_balance' => 'debit'],
                ['code' => '512', 'name' => 'Beban Depresiasi Peralatan', 'type' => 'expense', 'normal_balance' => 'debit'],
                ['code' => '513', 'name' => 'Beban Depresiasi Komputer', 'type' => 'expense', 'normal_balance' => 'debit'],
                ['code' => '514', 'name' => 'Beban Depresiasi Furniture', 'type' => 'expense', 'normal_balance' => 'debit'],
                ['code' => '515', 'name' => 'Beban Depresiasi Bangunan', 'type' => 'expense', 'normal_balance' => 'debit'],
            ];

            foreach ($accounts as $a) {
                Account::updateOrCreate(
                    ['company_id' => $company->id, 'code' => $a['code']],
                    array_merge($a, [
                        'company_id' => $company->id,
                        'is_active' => true,
                    ])
                );
            }
        }

        $this->command->info('COA aset tetap dibuat untuk ' . Company::count() . ' company.');
    }
}