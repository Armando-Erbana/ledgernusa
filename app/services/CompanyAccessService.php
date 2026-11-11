<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

class CompanyAccessService
{
    /**
     * Ambil plan aktif user (dari subscription trial atau berbayar).
     */
    public function activePlan(User $user): ?Plan
    {
        $companyIds = $user->companies()->pluck('companies.id');

        if ($companyIds->isEmpty()) {
            return null;
        }

        $sub = Subscription::whereIn('company_id', $companyIds)
            ->where(function ($q) {
                $q->where(fn ($s) => $s->where('status', 'trial')->where('trial_ends_at', '>=', now()))
                  ->orWhere(fn ($s) => $s->where('status', 'active')->where('ends_at', '>=', now()));
            })
            ->with('plan')
            ->latest('id')
            ->first();

        return $sub?->plan;
    }

    public function maxCompanies(User $user): int
    {
        $plan = $this->activePlan($user);
        return $plan?->max_companies ?? 0;
    }

    /**
     * Company yang "terkunci" karena melebihi kuota plan.
     * Urutan: company_users.created_at ASC — yang paling awal = prioritas.
     */
    public function lockedCompanyIds(User $user): array
    {
        $max = $this->maxCompanies($user);
        if ($max <= 0) {
            return $user->companies()->pluck('companies.id')->toArray();
        }

        $allIds = $user->companies()
            ->orderBy('company_users.created_at')
            ->pluck('companies.id')
            ->toArray();

        return array_slice($allIds, $max);
    }

    public function canAccess(User $user, int $companyId): bool
    {
        return ! in_array($companyId, $this->lockedCompanyIds($user), true);
    }

    public function remainingSlots(User $user): int
    {
        return max(0, $this->maxCompanies($user) - $user->companies()->count());
    }
}