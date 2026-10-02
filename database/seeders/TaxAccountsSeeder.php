<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Company;
use Illuminate\Database\Seeder;

class TaxAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $accounts = [
                // PPN
                ['code' => '211', 'name' => 'PPN Keluaran', 'type' => 'liability', 'normal_balance' => 'credit'],
                ['code' => '212', 'name' => 'PPN Masukan', 'type' => 'asset', 'normal_balance' => 'debit'],
                ['code' => '213', 'name' => 'PPN Kurang Bayar', 'type' => 'liability', 'normal_balance' => 'credit'],
                ['code' => '214', 'name' => 'PPN Lebih Bayar', 'type' => 'asset', 'normal_balance' => 'debit'],

                // PPh
                ['code' => '221', 'name' => 'Utang PPh 21', 'type' => 'liability', 'normal_balance' => 'credit'],
                ['code' => '222', 'name' => 'Utang PPh 23', 'type' => 'liability', 'normal_balance' => 'credit'],
                ['code' => '223', 'name' => 'Utang PPh 4(2)', 'type' => 'liability', 'normal_balance' => 'credit'],
                ['code' => '224', 'name' => 'Utang PPh 25', 'type' => 'liability', 'normal_balance' => 'credit'],
                ['code' => '225', 'name' => 'Utang PPh 29', 'type' => 'liability', 'normal_balance' => 'credit'],
            ];

            foreach ($accounts as $a) {
                Account::updateOrCreate(
                    ['company_id' => $company->id, 'code' => $a['code']],
                    array_merge($a, ['company_id' => $company->id, 'is_active' => true])
                );
            }
        }

        $this->command->info('✅ COA pajak dibuat untuk ' . $companies->count() . ' company.');
    }
}