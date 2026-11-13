<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToCompany;

class Journal extends Model
{
    use HasFactory, BelongsToCompany, Auditable;

    protected $fillable = [
        'company_id', 'branch_id', 'date', 'reference', 'description', 'status',
        'created_by', 'posted_by', 'posted_at',
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

        protected static function booted()
    {
        static::creating(function ($journal) {
            $company = \App\Models\Company::find($journal->company_id);
            if ($company && $company->closed_until && $journal->date <= $company->closed_until) {
                throw new \Exception('Transaksi ditolak: Periode buku untuk tanggal ini sudah ditutup.');
            }
        });

        static::updating(function ($journal) {
            $company = \App\Models\Company::find($journal->company_id);
            if ($company && $company->closed_until && $journal->date <= $company->closed_until) {
                throw new \Exception('Transaksi ditolak: Jurnal ini berada di periode yang sudah ditutup.');
            }
        });
    }
}