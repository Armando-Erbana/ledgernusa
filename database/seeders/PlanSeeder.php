<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'code' => 'starter',
                'name' => 'Starter',
                'description' => 'Untuk usaha kecil yang baru mulai',
                'price_monthly' => 149000,
                'price_yearly' => 1490000,
                'max_users' => 3,
                'max_journals_per_month' => 500,
                'max_companies' => 1,
                'features' => json_encode(['Laporan dasar', 'Multi-user basic']),
                'sort_order' => 1,
            ],
            [
                'code' => 'pro',
                'name' => 'Professional',
                'description' => 'Untuk usaha yang sedang berkembang',
                'price_monthly' => 399000,
                'price_yearly' => 3990000,
                'max_users' => 10,
                'max_journals_per_month' => 5000,
                'max_companies' => 3,
                'features' => json_encode(['Semua laporan', 'Approval jurnal', 'Audit log']),
                'sort_order' => 2,
            ],
            [
                'code' => 'enterprise',
                'name' => 'Enterprise',
                'description' => 'Untuk korporasi & multi-cabang',
                'price_monthly' => 1500000,
                'price_yearly' => 15000000,
                'max_users' => -1,
                'max_journals_per_month' => -1,
                'max_companies' => -1,
                'features' => json_encode(['Unlimited', 'API', 'Support prioritas']),
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $p) {
            DB::table('plans')->updateOrInsert(['code' => $p['code']], array_merge($p, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}