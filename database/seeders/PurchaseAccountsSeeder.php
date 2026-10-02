<?php
namespace Database\Seeders;

use App\Models\Account;
use App\Models\Company;
use Illuminate\Database\Seeder;

class PurchaseAccountsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Company::all() as $company) {
            $accounts = [
                ['code' => '104', 'name' => 'Persediaan Barang', 'type' => 'asset', 'normal_balance' => 'debit'],
                ['code' => '201', 'name' => 'Hutang Usaha', 'type' => 'liability', 'normal_balance' => 'credit'],
                ['code' => '502', 'name' => 'Beban Pembelian', 'type' => 'expense', 'normal_balance' => 'debit'],
                ['code' => '503', 'name' => 'Beban Sewa', 'type' => 'expense', 'normal_balance' => 'debit'],
                ['code' => '504', 'name' => 'Beban Listrik & Air', 'type' => 'expense', 'normal_balance' => 'debit'],
                ['code' => '505', 'name' => 'Beban Internet & Telepon', 'type' => 'expense', 'normal_balance' => 'debit'],
            ];

            foreach ($accounts as $a) {
                Account::updateOrCreate(
                    ['company_id' => $company->id, 'code' => $a['code']],
                    array_merge($a, ['company_id' => $company->id, 'is_active' => true])
                );
            }
        }
        $this->command->info('COA pembelian dibuat.');
    }
}