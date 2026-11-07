<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'code' => 'starter',
                'name' => 'Starter',
                'description' => 'Untuk UMKM & freelance — 1 user, 1 perusahaan, fitur dasar',
                'price_monthly' => 175000,
                'price_yearly' => 1890000,
                'duration_days' => 30,
                'max_users' => 1,
                'max_companies' => 1,
                'max_journals_per_month' => 100,
                'sort_order' => 1,
                'is_active' => true,
                'features' => [
                    'coa' => true, 'journals' => true, 'cash_bank' => true, 'transfers' => true,
                    'sales' => true, 'purchases' => true, 'reports_basic' => true,
                    'aging' => false, 'inventory' => false, 'orders' => false, 'bank_rec' => false,
                    'multi_currency' => false, 'fixed_assets' => false, 'payroll' => false, 'tax' => false,
                    'export' => false, 'ai' => false, 'audit_log' => false,
                    'consolidation' => false, 'budgeting' => false, 'efaktur' => false, 'midtrans' => false,
                ],
            ],
            [
                'code' => 'professional',
                'name' => 'Professional',
                'description' => 'Untuk bisnis berkembang — 5 user, 3 perusahaan, semua fitur',
                'price_monthly' => 375000,
                'price_yearly' => 4050000,
                'duration_days' => 30,
                'max_users' => 5,
                'max_companies' => 3,
                'max_journals_per_month' => 5000,
                'sort_order' => 2,
                'is_active' => true,
                'features' => [
                    'coa' => true, 'journals' => true, 'cash_bank' => true, 'transfers' => true,
                    'sales' => true, 'purchases' => true, 'reports_basic' => true,
                    'aging' => true, 'inventory' => true, 'orders' => true, 'bank_rec' => true,
                    'multi_currency' => true, 'fixed_assets' => true, 'payroll' => true, 'tax' => true,
                    'export' => true, 'ai' => true, 'audit_log' => true,
                    'consolidation' => false, 'budgeting' => false, 'efaktur' => false, 'midtrans' => false,
                ],
            ],
            [
                'code' => 'enterprise',
                'name' => 'Enterprise',
                'description' => 'Untuk grup perusahaan — unlimited user & company, semua fitur + prioritas support',
                'price_monthly' => 600000,
                'price_yearly' => 6480000,
                'duration_days' => 60,
                'max_users' => 999999,
                'max_companies' => 999999,
                'max_journals_per_month' => 999999,
                'sort_order' => 3,
                'is_active' => true,
                'features' => [
                    'coa' => true, 'journals' => true, 'cash_bank' => true, 'transfers' => true,
                    'sales' => true, 'purchases' => true, 'reports_basic' => true,
                    'aging' => true, 'inventory' => true, 'orders' => true, 'bank_rec' => true,
                    'multi_currency' => true, 'fixed_assets' => true, 'payroll' => true, 'tax' => true,
                    'export' => true, 'ai' => true, 'audit_log' => true,
                    'consolidation' => true, 'budgeting' => true, 'efaktur' => true, 'midtrans' => true,
                ],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['code' => $plan['code']], $plan);
        }
    }
}