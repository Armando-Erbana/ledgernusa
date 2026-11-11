<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class BranchScope
{
    /**
     * Terapkan filter cabang ke query.
     * - Staff: SELALU terkunci ke cabang session.
     * - Owner/Admin: hanya difilter jika sedang melihat 1 cabang.
     */
    public function apply(Builder $query, string $column = 'branch_id'): Builder
    {
        $branchId = session('branch_id');
        $user = auth()->user();

        if ($user && $user->roleInActiveCompany() === 'staff') {
            return $branchId
                ? $query->where($column, $branchId)
                : $query->whereNull($column);
        }

        if ($this->isConsolidated()) {
            return $query;
        }

        return $query->where($column, $branchId);
    }

    public function isConsolidated(): bool
    {
        $user = auth()->user();

        if ($user && $user->roleInActiveCompany() === 'staff') {
            return false;
        }

        $branchId = session('branch_id');

        return blank($branchId) || (int) $branchId === 0;
    }

    public function currentBranchId(): ?int
    {
        return session('branch_id') ? (int) session('branch_id') : null;
    }
}