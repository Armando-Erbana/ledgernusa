<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id', 'sale_id', 'journal_id', 'date', 'cash_account_id',
        'amount', 'reference', 'notes', 'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function sale() { return $this->belongsTo(Sale::class); }
    public function cashAccount() { return $this->belongsTo(Account::class, 'cash_account_id'); }
    public function journal() { return $this->belongsTo(Journal::class); }

    public function generateJournal(): Journal
    {
        $companyId = $this->company_id;

        $arAccount = Account::where('company_id', $companyId)
            ->where('code', 'like', '103%')
            ->orWhere(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->where('type', 'asset')->where('name', 'like', '%piutang%');
            })
            ->first();

        $journal = Journal::create([
            'company_id' => $companyId,
            'date' => $this->date,
            'reference' => $this->reference ?: 'PAY-' . $this->id,
            'description' => 'Pembayaran penjualan ' . ($this->sale->invoice_number ?? ''),
            'status' => 'posted',
            'created_by' => $this->created_by,
            'posted_by' => $this->created_by,
            'posted_at' => now(),
        ]);

        $journal->entries()->createMany([
            [
                'account_id' => $this->cash_account_id,
                'debit' => $this->amount,
                'credit' => 0,
                'description' => 'Kas diterima',
            ],
            [
                'account_id' => $arAccount?->id,
                'debit' => 0,
                'credit' => $this->amount,
                'description' => 'Pelunasan piutang',
            ],
        ]);

        return $journal;
    }
}