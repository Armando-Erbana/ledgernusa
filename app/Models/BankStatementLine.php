<?php
namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class BankStatementLine extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'bank_statement_id', 'company_id', 'date', 'description', 'reference',
        'debit', 'credit', 'balance', 'match_status',
        'matched_type', 'matched_id', 'matched_at', 'matched_by',
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'balance' => 'decimal:2',
        'matched_at' => 'datetime',
    ];

    public function statement() { return $this->belongsTo(BankStatement::class, 'bank_statement_id'); }
    public function matchedByUser() { return $this->belongsTo(User::class, 'matched_by'); }

    public function matchedTransaction()
    {
        if (!$this->matched_type || !$this->matched_id) return null;
        return match ($this->matched_type) {
            'cash_transaction' => CashTransaction::find($this->matched_id),
            'sale_payment' => SalePayment::find($this->matched_id),
            'purchase_payment' => PurchasePayment::find($this->matched_id),
            default => null,
        };
    }
}