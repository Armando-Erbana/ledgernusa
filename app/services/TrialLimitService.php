<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\Subscription;
use App\Models\User;

class TrialLimitService
{
    public const MAX_JOURNALS = 1000;

    /**
     * Hitung total jurnal user (lintas semua company).
     * Setiap transaksi (kas, penjualan, pembelian, payroll) = 1 jurnal.
     */
    public function journalCount(User $user): int
    {
        $companyIds = $user->companies()->pluck('companies.id');

        if ($companyIds->isEmpty()) {
            return 0;
        }

        return Journal::whereIn('company_id', $companyIds)->count();
    }

    public function isLimitReached(User $user): bool
    {
        return $this->journalCount($user) >= self::MAX_JOURNALS;
    }

    public function isOnTrial(User $user): bool
    {
        return Subscription::whereIn('company_id', $user->companies()->pluck('companies.id'))
            ->where('status', 'trial')
            ->where('trial_ends_at', '>=', now())
            ->exists();
    }

    /**
     * Read-only jika sedang trial DAN limit tercapai.
     */
    public function shouldBeReadOnly(User $user): bool
    {
        return $this->isOnTrial($user) && $this->isLimitReached($user);
    }

    public function remaining(User $user): int
    {
        return max(0, self::MAX_JOURNALS - $this->journalCount($user));
    }
}