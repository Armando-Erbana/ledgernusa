<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    protected $fillable = [
        'company_id',
        'date',
        'currency',
        'exchange_rate',
        'reference',
        'description',
        'status',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'date' => 'date',
        'exchange_rate' => 'decimal:8',
        'posted_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function getTotalDebitAttribute(): float
    {
        return (float) $this->entries->sum('debit');
    }

    public function getTotalCreditAttribute(): float
    {
        return (float) $this->entries->sum('credit');
    }

    public function getIsBalancedAttribute(): bool
    {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }
}