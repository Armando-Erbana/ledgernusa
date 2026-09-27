<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id','parent_id','code','name','type',
        'normal_balance','is_active','is_cash','is_bank',
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'is_cash' => 'boolean',
        'is_bank' => 'boolean',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function parent() { return $this->belongsTo(Account::class, 'parent_id'); }
    public function children() { return $this->hasMany(Account::class, 'parent_id'); }
    public function entries() { return $this->hasMany(JournalEntry::class); }

    public function getBalanceAttribute()
    {
        $debit = $this->entries()->sum('debit');
        $credit = $this->entries()->sum('credit');
        return $this->normal_balance === 'debit' ? ($debit - $credit) : ($credit - $debit);
    }
}