<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\Cache;

trait FlushesDashboardCache
{
    protected function flushDashboardCache(): void
    {
        $companyId = session('company_id');
        if (! $companyId) return;

        Cache::forget("dashboard.v2.{$companyId}.all");

        $branchId = session('branch_id');
        if ($branchId) {
            Cache::forget("dashboard.v2.{$companyId}.{$branchId}");
        }
    }
}