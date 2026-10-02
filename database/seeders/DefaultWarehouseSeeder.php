<?php
namespace Database\Seeders;

use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DefaultWarehouseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Company::all() as $company) {
            Warehouse::firstOrCreate(
                ['company_id' => $company->id, 'code' => 'GDG-01'],
                ['name' => 'Gudang Utama', 'is_default' => true, 'is_active' => true]
            );
        }
        $this->command->info('✅ Gudang default dibuat.');
    }
}