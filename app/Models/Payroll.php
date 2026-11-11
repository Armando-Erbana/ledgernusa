<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'branch_id', 'payroll_number', 'period_month', 'period_year', 
        'payment_date', 'total_gross', 'total_pph21', 'total_bpjs', 'total_net', 
        'status', 'notes', 'journal_id'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'total_gross' => 'decimal:2',
        'total_pph21' => 'decimal:2',
        'total_bpjs' => 'decimal:2',
        'total_net' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function items() { return $this->hasMany(PayrollItem::class); }
    public function journal() { return $this->belongsTo(Journal::class); }

    public function generateJournal(): Journal
    {
        $companyId = $this->company_id;

        // 1. Cari Akun Beban Gaji
        $expenseAccount = Account::where('company_id', $companyId)->where('code', '5-1000')->first();
        if (!$expenseAccount) {
            $expenseAccount = Account::where('company_id', $companyId)
                ->where('type', 'expense')
                ->where('name', 'like', '%Gaji%')
                ->first();
        }

        // 2. Cari Akun Kas/Bank
        $cashAccount = Account::where('company_id', $companyId)->where('code', '1-1000')->first();
        if (!$cashAccount) {
            $cashAccount = Account::where('company_id', $companyId)
                ->where('type', 'asset')
                ->where(function($q) {
                    $q->where('name', 'like', '%Kas%')->orWhere('name', 'like', '%Bank%');
                })->first();
        }

        // 3. Cari Akun Utang PPh 21 & BPJS
        $taxAccount = Account::where('company_id', $companyId)->where('name', 'like', '%PPh 21%')->first();
        $bpjsAccount = Account::where('company_id', $companyId)->where('name', 'like', '%BPJS%')->first();

        if (!$expenseAccount || !$cashAccount) {
            throw new \Exception("Gagal membuat Jurnal. Pastikan Anda sudah memiliki Akun bertipe 'Beban' bernama 'Beban Gaji' dan Akun bertipe 'Aset' bernama 'Kas' di menu COA.");
        }

        $journal = Journal::create([
            'company_id' => $companyId,
            'branch_id'   => $this->branch_id,
            'branch_id' => $this->branch_id ?? session('branch_id'),
            'date' => $this->payment_date ?? now(),
            'reference' => 'PAYROLL-' . $this->payroll_number,
            'description' => 'Jurnal Gaji Periode ' . $this->period_month . '/' . $this->period_year,
            'status' => 'posted', // Langsung posted
        ]);

        // Jurnal Debit: Beban Gaji (Gross)
        $journal->entries()->create([
            'account_id' => $expenseAccount->id,
            'debit' => $this->total_gross,
            'credit' => 0,
        ]);

        // Jurnal Kredit: Utang PPh 21
        if ($this->total_pph21 > 0 && $taxAccount) {
            $journal->entries()->create([
                'account_id' => $taxAccount->id,
                'debit' => 0,
                'credit' => $this->total_pph21,
            ]);
        }

        // Jurnal Kredit: Utang BPJS
        if ($this->total_bpjs > 0 && $bpjsAccount) {
            $journal->entries()->create([
                'account_id' => $bpjsAccount->id,
                'debit' => 0,
                'credit' => $this->total_bpjs,
            ]);
        }

        // Jurnal Kredit: Kas/Bank (Net)
        $journal->entries()->create([
            'account_id' => $cashAccount->id,
            'debit' => 0,
            'credit' => $this->total_net,
        ]);

        return $journal;
    }
}