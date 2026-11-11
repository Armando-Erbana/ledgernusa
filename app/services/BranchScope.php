<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class BranchScope
{
    public function apply(Builder $query, string $column = 'branch_id'): Builder
    {
        $branchId = session('branch_id');
        $user = auth()->user();

        // Staff selalu terkunci ke cabangnya
        if ($user && $user->roleInActiveCompany() === 'staff') {
            return $branchId ? $query->where($column, $branchId) : $query->whereNull($column);
        }

        // Owner/Admin: Jika sedang di mode Konsolidasi (session branch_id kosong/0)
        if (blank($branchId) || (int) $branchId === 0) {
            return $query; // Tampilkan semua
        }

        return $query->where($column, $branchId);
    }

    public function isConsolidated(): bool
    {
        $branchId = session('branch_id');
        return blank($branchId) || (int) $branchId === 0;
    }
}