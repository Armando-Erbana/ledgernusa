<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id', 'journal_id', 'date', 'reference',
        'from_account_id', 'to_account_id', 'amount', 'admin_fee',
        'description', 'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'admin_fee' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function journal() { return $this->belongsTo(Journal::class); }
    public function fromAccount() { return $this->belongsTo(Account::class, 'from_account_id'); }
    public function toAccount() { return $this->belongsTo(Account::class, 'to_account_id'); }

    public function generateJournal(): Journal
    {
        $journal = Journal::create([
            'company_id' => $this->company_id,
            'date' => $this->date,
            'reference' => $this->reference ?: 'TRF-' . $this->id,
            'description' => $this->description ?: 'Transfer antar akun',
            'status' => 'posted',
            'created_by' => $this->created_by,
            'posted_by' => $this->created_by,
            'posted_at' => now(),
        ]);

        $entries = [
            [
                'account_id' => $this->to_account_id,
                'debit' => $this->amount,
                'credit' => 0,
                'description' => 'Transfer masuk',
            ],
            [
                'account_id' => $this->from_account_id,
                'debit' => 0,
                'credit' => $this->amount,
                'description' => 'Transfer keluar',
            ],
        ];

        if ($this->admin_fee > 0) {
            $feeAccount = Account::where('company_id', $this->company_id)
                ->where('type', 'expense')
                ->where('is_active', true)
                ->orderBy('code')
                ->first();

            if ($feeAccount) {
                $entries[] = [
                    'account_id' => $feeAccount->id,
                    'debit' => $this->admin_fee,
                    'credit' => 0,
                    'description' => 'Biaya admin transfer',
                ];
                $entries[1]['credit'] += $this->admin_fee;
            }
        }

        $journal->entries()->createMany($entries);

        return $journal;
    }
}