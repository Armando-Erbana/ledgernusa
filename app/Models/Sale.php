<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id', 'journal_id', 'invoice_number', 'customer_id',
        'date', 'due_date', 'type', 'subtotal', 'discount', 'tax',
        'total', 'paid_amount', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items() { return $this->hasMany(SaleItem::class); }
    public function payments() { return $this->hasMany(SalePayment::class); }
    public function journal() { return $this->belongsTo(Journal::class); }

    public function getBalanceAttribute()
    {
        return $this->total - $this->paid_amount;
    }

    public function generateJournal(): Journal
    {
        $companyId = $this->company_id;

        $arAccount = Account::where('company_id', $companyId)
            ->where('code', 'like', '103%')
            ->orWhere(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->where('type', 'asset')->where('name', 'like', '%piutang%');
            })
            ->first()
            ?? Account::where('company_id', $companyId)->where('type', 'asset')->orderBy('code')->first();

        $journal = Journal::create([
            'company_id' => $companyId,
            'date' => $this->date,
            'reference' => $this->invoice_number,
            'description' => 'Penjualan ' . ($this->customer?->name ?? ''),
            'status' => 'posted',
            'created_by' => $this->created_by,
            'posted_by' => $this->created_by,
            'posted_at' => now(),
        ]);

        $entries = [];

        if ($this->type === 'credit' && $arAccount) {
            $entries[] = [
                'account_id' => $arAccount->id,
                'debit' => $this->total,
                'credit' => 0,
                'description' => 'Piutang usaha',
            ];
        }

        foreach ($this->items as $item) {
            $entries[] = [
                'account_id' => $item->account_id,
                'debit' => 0,
                'credit' => $item->subtotal - $item->discount,
                'description' => $item->description,
            ];
        }

        if ($this->tax > 0) {
    // Cari akun PPN Keluaran (kode 211)
    $taxAccount = Account::where('company_id', $companyId)
        ->where('code', '211')
        ->first()
        ?? Account::where('company_id', $companyId)
            ->where('type', 'liability')
            ->where('name', 'like', '%ppn keluaran%')
            ->first()
        ?? Account::where('company_id', $companyId)
            ->where('type', 'liability')
            ->where('name', 'like', '%pajak%')
            ->first();

    if ($taxAccount) {
        $entries[] = [
            'account_id' => $taxAccount->id,
            'debit' => 0,
            'credit' => $this->tax,
            'description' => 'PPN Keluaran',
        ];
    }
}

        $journal->entries()->createMany($entries);

        return $journal;
    }
}