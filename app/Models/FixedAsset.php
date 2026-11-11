<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToCompany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    use HasFactory, BelongsToCompany, Auditable;

    protected $fillable = [
        'company_id', 'journal_id', 'code', 'name', 'category', 'description', 'location',
        'asset_account_id', 'depreciation_account_id', 'expense_account_id',
        'purchase_date', 'purchase_cost', 'residual_value', 'useful_life_months',
        'depreciation_method', 'accumulated_depreciation', 'book_value',
        'last_depreciation_date', 'status', 'disposed_at', 'disposal_value', 'created_by',
        'down_payment', 'loan_amount', 'tenor_months', 'interest_rate', 'liability_account_id'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'last_depreciation_date' => 'date',
        'disposed_at' => 'date',
        'purchase_cost' => 'decimal:2',
        'residual_value' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'book_value' => 'decimal:2',
        'disposal_value' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'loan_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function assetAccount() { return $this->belongsTo(Account::class, 'asset_account_id'); }
    public function depreciationAccount() { return $this->belongsTo(Account::class, 'depreciation_account_id'); }
    public function expenseAccount() { return $this->belongsTo(Account::class, 'expense_account_id'); }
    public function liabilityAccount() { return $this->belongsTo(Account::class, 'liability_account_id'); }
    public function journal() { return $this->belongsTo(Journal::class); }
    public function depreciations() { return $this->hasMany(AssetDepreciation::class); }

    public function monthlyDepreciation(): float
    {
        $depreciable = (float) $this->purchase_cost - (float) $this->residual_value;
        if ($this->useful_life_months <= 0) return 0;
        return round($depreciable / $this->useful_life_months, 2);
    }

    public function monthsDepreciated(): int
    {
        return $this->depreciations()->count();
    }

    public function isFullyDepreciated(): bool
    {
        return $this->monthsDepreciated() >= $this->useful_life_months;
    }

    public function generateAcquisitionJournal(): Journal
    {
        $journal = Journal::create([
            'company_id' => $this->company_id,
            'branch_id'   => $this->branch_id,
            'date' => $this->purchase_date,
            'reference' => 'AST-' . ($this->code ?: $this->id),
            'description' => 'Perolehan aset: ' . $this->name,
            'status' => 'posted',
            'created_by' => $this->created_by,
            'posted_by' => $this->created_by,
            'posted_at' => now(),
        ]);

        $cashAccount = Account::where('company_id', $this->company_id)
            ->where(function ($q) {
                $q->where('is_cash', true)->orWhere('is_bank', true);
            })
            ->orderBy('code')
            ->first();

        $entries = [
            [
                'account_id' => $this->asset_account_id,
                'debit' => $this->purchase_cost,
                'credit' => 0,
                'description' => 'Perolehan ' . $this->name,
            ]
        ];

        // Jika ada DP, Kredit Kas
        if ($this->down_payment > 0 && $cashAccount) {
            $entries[] = [
                'account_id' => $cashAccount->id,
                'debit' => 0,
                'credit' => $this->down_payment,
                'description' => 'DP Pembelian ' . $this->name,
            ];
        }

        // Jika ada Sisa Leasing, Kredit Utang
        if ($this->loan_amount > 0 && $this->liability_account_id) {
            $entries[] = [
                'account_id' => $this->liability_account_id,
                'debit' => 0,
                'credit' => $this->loan_amount,
                'description' => 'Utang Leasing ' . $this->name,
            ];
        } else {
            // Jika tidak ada Leasing (Beli Cash), maka sisanya masuk ke Kas
            $sisaCash = $this->purchase_cost - $this->down_payment;
            if ($sisaCash > 0 && $cashAccount) {
                $entries[] = [
                    'account_id' => $cashAccount->id,
                    'debit' => 0,
                    'credit' => $sisaCash,
                    'description' => 'Pelunasan Tunai ' . $this->name,
                ];
            }
        }

        $journal->entries()->createMany($entries);

        return $journal;
    }

    public function generateDepreciationJournal(Carbon $periodDate, float $amount): Journal
    {
        $journal = Journal::create([
            'company_id' => $this->company_id,
            'date' => $periodDate,
            'reference' => 'DEP-' . ($this->code ?: $this->id) . '-' . $periodDate->format('Ym'),
            'description' => 'Depresiasi ' . $periodDate->format('M Y') . ': ' . $this->name,
            'status' => 'posted',
            'created_by' => $this->created_by,
            'posted_by' => $this->created_by,
            'posted_at' => now(),
        ]);

        $journal->entries()->createMany([
            [
                'account_id' => $this->expense_account_id,
                'debit' => $amount,
                'credit' => 0,
                'description' => 'Beban depresiasi ' . $this->name,
            ],
            [
                'account_id' => $this->depreciation_account_id,
                'debit' => 0,
                'credit' => $amount,
                'description' => 'Akumulasi depresiasi ' . $this->name,
            ],
        ]);

        return $journal;
    }
}