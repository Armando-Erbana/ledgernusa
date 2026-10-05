<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRate extends Model
{
    protected $fillable = [
        'company_id',
        'from_currency',
        'to_currency',
        'rate',
        'effective_date',
        'source',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'rate' => 'decimal:8',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForPair($query, int $companyId, string $from, string $to)
    {
        return $query->where('company_id', $companyId)
            ->where('from_currency', $from)
            ->where('to_currency', $to);
    }
}