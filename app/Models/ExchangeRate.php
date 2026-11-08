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
        'rate' => 'float',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Filter berdasarkan company + pasangan mata uang.
     * Input otomatis di-uppercase agar konsisten.
     */
    public function scopeForPair($query, int $companyId, string $from, string $to)
    {
        return $query->where('company_id', $companyId)
            ->where('from_currency', strtoupper($from))
            ->where('to_currency', strtoupper($to));
    }

    /**
     * Urutkan kurs terbaru untuk pasangan tertentu.
     */
    public function scopeLatestRate($query, int $companyId, string $from, string $to)
    {
        return $query->forPair($companyId, $from, $to)
            ->orderByDesc('effective_date')
            ->orderByDesc('id');
    }

    /**
     * Ambil kurs terbaru, atau null jika belum ada.
     */
    public static function findLatest(int $companyId, string $from, string $to): ?self
    {
        return static::latestRate($companyId, $from, $to)->first();
    }
}