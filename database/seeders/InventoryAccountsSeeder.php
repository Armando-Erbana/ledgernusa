<?php
namespace Database\Seeders;

use App\Models\Account;
use App\Models\Company;
use Illuminate\Database\Seeder;

class InventoryAccountsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Company::all() as $company) {
            $accounts = [
                ['code' => '104', 'name' => 'Persediaan Barang Dagang', 'type' => 'asset', 'normal_balance' => 'debit'],
                ['code' => '401', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue', 'normal_balance' => 'credit'],
                ['code' => '402', 'name' => 'Pendapatan Jasa', 'type' => 'revenue', 'normal_balance' => 'credit'],
                ['code' => '501', 'name' => 'Harga Pokok Penjualan (HPP)', 'type' => 'expense', 'normal_balance' => 'debit'],
                ['code' => '502', 'name' => 'Beban Pembelian', 'type' => 'expense', 'normal_balance' => 'debit'],
                ['code' => '521', 'name' => 'Beban Selisih Stok', 'type' => 'expense', 'normal_balance' => 'debit'],
            ];

            foreach ($accounts as $a) {
                Account::updateOrCreate(
                    ['company_id' => $company->id, 'code' => $a['code']],
                    array_merge($a, ['company_id' => $company->id, 'is_active' => true])
                );
            }
        }

        $this->command->info('✅ COA persediaan dibuat.');
    }
}