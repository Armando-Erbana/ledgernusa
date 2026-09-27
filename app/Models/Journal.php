<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id','date','reference','description','status',
        'created_by','posted_by','posted_at',
    ];
    protected $casts = [
        'date' => 'date',
        'posted_at' => 'datetime',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function entries() { return $this->hasMany(JournalEntry::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function poster() { return $this->belongsTo(User::class, 'posted_by'); }

    public function totalDebit() { return $this->entries->sum('debit'); }
    public function totalCredit() { return $this->entries->sum('credit'); }
}