<?php
namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory, BelongsToCompany;
    protected $fillable = [
        'company_id','journal_id','bill_number','supplier_id','date','due_date','type',
        'subtotal','discount','tax','total','paid_amount','status','notes','created_by',
    ];
    protected $casts = [
        'date' => 'date', 'due_date' => 'date',
        'subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'tax' => 'decimal:2',
        'total' => 'decimal:2', 'paid_amount' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function items() { return $this->hasMany(PurchaseItem::class); }
    public function payments() { return $this->hasMany(PurchasePayment::class); }
    public function journal() { return $this->belongsTo(Journal::class); }

    public function getBalanceAttribute() { return $this->total - $this->paid_amount; }

    public function generateJournal(): Journal
    {
        $companyId = $this->company_id;

        // Akun Hutang Usaha (kode 201)
        $apAccount = Account::where('company_id', $companyId)->where('code', '201')->first()
            ?? Account::where('company_id', $companyId)->where('type', 'liability')->where('name', 'like', '%hutang%')->first();

        // Akun PPN Masukan (kode 212)
        $ppnInAccount = Account::where('company_id', $companyId)->where('code', '212')->first();

        $journal = Journal::create([
            'company_id' => $companyId,
            'date' => $this->date,
            'reference' => $this->bill_number,
            'description' => 'Pembelian dari ' . ($this->supplier?->name ?? '-'),
            'status' => 'posted',
            'created_by' => $this->created_by,
            'posted_by' => $this->created_by,
            'posted_at' => now(),
        ]);

        $entries = [];

        // Debit: akun beban/persediaan per item
        foreach ($this->items as $item) {
            $entries[] = [
                'account_id' => $item->account_id,
                'debit' => $item->subtotal - $item->discount,
                'credit' => 0,
                'description' => $item->description,
            ];
        }

        // Debit: PPN Masukan
        if ($this->tax > 0 && $ppnInAccount) {
            $entries[] = [
                'account_id' => $ppnInAccount->id,
                'debit' => $this->tax,
                'credit' => 0,
                'description' => 'PPN Masukan',
            ];
        }

        // Kredit: Hutang / Kas
        if ($this->type === 'credit' && $apAccount) {
            $entries[] = [
                'account_id' => $apAccount->id,
                'debit' => 0,
                'credit' => $this->total,
                'description' => 'Hutang usaha',
            ];
        } else {
            $cashAccount = Account::where('company_id', $companyId)
                ->where(fn($q) => $q->where('is_cash', true)->orWhere('is_bank', true))
                ->orderBy('code')->first();
            $entries[] = [
                'account_id' => $cashAccount?->id,
                'debit' => 0,
                'credit' => $this->total,
                'description' => 'Pembayaran tunai',
            ];
        }

        $journal->entries()->createMany($entries);
        return $journal;
    }
}