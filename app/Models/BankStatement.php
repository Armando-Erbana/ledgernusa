<?php
namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class BankStatement extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'account_id', 'period_start', 'period_end',
        'file_name', 'opening_balance', 'closing_balance', 'status', 'created_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function account() { return $this->belongsTo(Account::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function lines() { return $this->hasMany(BankStatementLine::class); }

    public function getMatchedCountAttribute(): int
    {
        return $this->lines()->where('match_status', 'matched')->count();
    }

    public function getUnmatchedCountAttribute(): int
    {
        return $this->lines()->where('match_status', 'unmatched')->count();
    }
}