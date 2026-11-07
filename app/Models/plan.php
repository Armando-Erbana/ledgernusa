<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'code', 'name', 'description',
        'price_monthly', 'price_yearly', 'duration_days',
        'max_users', 'max_journals_per_month', 'max_companies',
        'features', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'is_active' => 'boolean',
        'max_users' => 'integer',
        'max_companies' => 'integer',
        'max_journals_per_month' => 'integer',
        'duration_days' => 'integer',
    ];

    // ============================================================
    // ACCESSOR: Backward compatibility
    // ============================================================

    /**
     * Accessor untuk $plan->price — ambil dari price_monthly.
     */
    public function getPriceAttribute(): float
    {
        return (float) $this->price_monthly;
    }

    /**
     * Accessor untuk $plan->slug — ambil dari code.
     */
    public function getSlugAttribute(): ?string
    {
        return $this->code;
    }

    // ============================================================
    // HELPER
    // ============================================================

    public function hasFeature(string $feature): bool
    {
        return (bool) ($this->features[$feature] ?? false);
    }

    public function isUnlimited(string $key): bool
    {
        $col = 'max_' . $key;
        return $this->{$col} === null;
    }
}