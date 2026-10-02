<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDepreciation extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id', 'fixed_asset_id', 'journal_id', 'period_date',
        'amount', 'accumulated_after', 'book_value_after', 'notes', 'created_by',
    ];

    protected $casts = [
        'period_date' => 'date',
        'amount' => 'decimal:2',
        'accumulated_after' => 'decimal:2',
        'book_value_after' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function fixedAsset() { return $this->belongsTo(FixedAsset::class); }
    public function journal() { return $this->belongsTo(Journal::class); }
}