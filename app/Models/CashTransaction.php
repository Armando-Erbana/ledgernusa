<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    use HasFactory, BelongsToCompany, Auditable;

    protected $fillable = [
        'company_id',
        'journal_id',
        'type',
        'date',
        'reference',
        'cash_account_id',
        'counter_account_id',
        'contact_id',
        'amount',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function cashAccount()
    {
        return $this->belongsTo(Account::class, 'cash_account_id');
    }

    public function counterAccount()
    {
        return $this->belongsTo(Account::class, 'counter_account_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Buat jurnal otomatis dari cash transaction.
     */
    public function generateJournal(): Journal
    {
        $journal = Journal::create([
            'company_id' => $this->company_id,
            'date' => $this->date,
            'reference' => $this->reference ?: 'KAS-' . $this->id,
            'description' => $this->description,
            'status' => 'posted',
            'created_by' => $this->created_by,
            'posted_by' => $this->created_by,
            'posted_at' => now(),
        ]);

        if ($this->type === 'in') {
            // Kas Masuk: Kas (D), Akun Lawan (K)
            $journal->entries()->createMany([
                [
                    'account_id' => $this->cash_account_id,
                    'debit' => $this->amount,
                    'credit' => 0,
                    'description' => $this->description,
                ],
                [
                    'account_id' => $this->counter_account_id,
                    'debit' => 0,
                    'credit' => $this->amount,
                    'description' => $this->description,
                ],
            ]);
        } else {
            // Kas Keluar: Akun Lawan (D), Kas (K)
            $journal->entries()->createMany([
                [
                    'account_id' => $this->counter_account_id,
                    'debit' => $this->amount,
                    'credit' => 0,
                    'description' => $this->description,
                ],
                [
                    'account_id' => $this->cash_account_id,
                    'debit' => 0,
                    'credit' => $this->amount,
                    'description' => $this->description,
                ],
            ]);
        }

        return $journal;
    }
}