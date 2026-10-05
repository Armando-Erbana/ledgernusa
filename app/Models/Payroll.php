<?php
namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory, BelongsToCompany, Auditable;
    protected $fillable = [
        'company_id','journal_id','payroll_number','period_month','period_year',
        'payment_date','total_gross','total_deduction','total_pph21','total_bpjs',
        'total_net','status','notes','created_by',
    ];
    protected $casts = [
        'payment_date' => 'date',
        'total_gross' => 'decimal:2','total_deduction' => 'decimal:2',
        'total_pph21' => 'decimal:2','total_bpjs' => 'decimal:2','total_net' => 'decimal:2',
    ];

    public function items() { return $this->hasMany(PayrollItem::class); }
    public function journal() { return $this->belongsTo(Journal::class); }

    public function generateJournal(): Journal
    {
        $companyId = $this->company_id;

        $journal = Journal::create([
            'company_id' => $companyId,
            'date' => $this->payment_date,
            'reference' => $this->payroll_number,
            'description' => 'Payroll ' . $this->period_month . '/' . $this->period_year,
            'status' => 'posted',
            'created_by' => $this->created_by,
            'posted_by' => $this->created_by,
            'posted_at' => now(),
        ]);

        // Cari akun-akun terkait
        $salaryAccount = Account::where('company_id', $companyId)->where('code', 'like', '5%')
            ->where('name', 'like', '%gaji%')->first()
            ?? Account::where('company_id', $companyId)->where('type', 'expense')->orderBy('code')->first();

        $pphAccount = Account::where('company_id', $companyId)->where('code', '221')->first();
        $bpjsAccount = Account::where('company_id', $companyId)->where('code', 'like', '22%')
            ->where('name', 'like', '%bpjs%')->first();
        $cashAccount = Account::where('company_id', $companyId)->where(function($q){
            $q->where('is_cash', true)->orWhere('is_bank', true);
        })->orderBy('code')->first();

        $entries = [];

        // Debit: Beban Gaji (gross)
        $entries[] = [
            'account_id' => $salaryAccount?->id,
            'debit' => $this->total_gross,
            'credit' => 0,
            'description' => 'Beban gaji ' . $this->period_month . '/' . $this->period_year,
        ];

        // Kredit: Utang PPh 21
        if ($this->total_pph21 > 0 && $pphAccount) {
            $entries[] = [
                'account_id' => $pphAccount->id,
                'debit' => 0,
                'credit' => $this->total_pph21,
                'description' => 'Utang PPh 21',
            ];
        }

        // Kredit: Utang BPJS
        if ($this->total_bpjs > 0 && $bpjsAccount) {
            $entries[] = [
                'account_id' => $bpjsAccount->id,
                'debit' => 0,
                'credit' => $this->total_bpjs,
                'description' => 'Utang BPJS',
            ];
        }

        // Kredit: Kas/Bank (net)
        $entries[] = [
            'account_id' => $cashAccount?->id,
            'debit' => 0,
            'credit' => $this->total_net,
            'description' => 'Pembayaran gaji',
        ];

        $journal->entries()->createMany($entries);
        return $journal;
    }
}