<?php
namespace Database\Seeders;

use App\Models\Account;
use App\Models\Company;
use Illuminate\Database\Seeder;

class PayrollAccountsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Company::all() as $company) {
            $accounts = [
                ['code' => '216', 'name' => 'Utang BPJS', 'type' => 'liability', 'normal_balance' => 'credit'],
                ['code' => '217', 'name' => 'Utang Gaji', 'type' => 'liability', 'normal_balance' => 'credit'],
                ['code' => '506', 'name' => 'Beban Gaji', 'type' => 'expense', 'normal_balance' => 'debit'],
                ['code' => '507', 'name' => 'Beban Tunjangan Karyawan', 'type' => 'expense', 'normal_balance' => 'debit'],
                ['code' => '508', 'name' => 'Beban BPJS Perusahaan', 'type' => 'expense', 'normal_balance' => 'debit'],
            ];
            foreach ($accounts as $a) {
                Account::updateOrCreate(
                    ['company_id' => $company->id, 'code' => $a['code']],
                    array_merge($a, ['company_id' => $company->id, 'is_active' => true])
                );
            }
        }
        $this->command->info('✅ COA payroll dibuat.');
    }
}