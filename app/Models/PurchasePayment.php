<?php
namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory, BelongsToCompany;
    protected $fillable = [
        'company_id','purchase_id','journal_id','date','cash_account_id','amount','pph23','reference','notes','created_by',
    ];
    protected $casts = ['date' => 'date','amount' => 'decimal:2','pph23' => 'decimal:2'];

    public function purchase() { return $this->belongsTo(Purchase::class); }
    public function cashAccount() { return $this->belongsTo(Account::class, 'cash_account_id'); }
    public function journal() { return $this->belongsTo(Journal::class); }

    public function generateJournal(): Journal
    {
        $companyId = $this->company_id;

        $apAccount = Account::where('company_id', $companyId)->where('code', '201')->first()
            ?? Account::where('company_id', $companyId)->where('type', 'liability')->where('name', 'like', '%hutang%')->first();

        $pphAccount = Account::where('company_id', $companyId)->where('code', '222')->first();

        $journal = Journal::create([
            'company_id' => $companyId,
            'date' => $this->date,
            'reference' => $this->reference ?: 'PAY-' . $this->id,
            'description' => 'Pembayaran pembelian ' . ($this->purchase->bill_number ?? ''),
            'status' => 'posted',
            'created_by' => $this->created_by,
            'posted_by' => $this->created_by,
            'posted_at' => now(),
        ]);

        $entries = [
            // Hutang (D)
            ['account_id' => $apAccount?->id, 'debit' => $this->amount + $this->pph23, 'credit' => 0, 'description' => 'Pelunasan hutang'],
            // Kas (K)
            ['account_id' => $this->cash_account_id, 'debit' => 0, 'credit' => $this->amount, 'description' => 'Kas keluar'],
        ];

        // PPh 23 dipotong (K)
        if ($this->pph23 > 0 && $pphAccount) {
            $entries[] = [
                'account_id' => $pphAccount->id,
                'debit' => 0,
                'credit' => $this->pph23,
                'description' => 'PPh 23 dipotong',
            ];
        }

        $journal->entries()->createMany($entries);
        return $journal;
    }
}