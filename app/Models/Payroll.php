<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- TAMBAHKAN BARIS INI
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payroll extends Model
{
    use HasFactory, BelongsToCompany, Auditable;

    protected $fillable = [
        'company_id', 'journal_id', 'payroll_number', 'period_month', 'period_year',
        'payment_date', 'total_gross', 'total_deduction', 'total_pph21', 'total_bpjs',
        'total_net', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'total_gross' => 'decimal:2', 
        'total_deduction' => 'decimal:2',
        'total_pph21' => 'decimal:2', 
        'total_bpjs' => 'decimal:2', 
        'total_net' => 'decimal:2',
    ];

    public function items() 
    { 
        return $this->hasMany(PayrollItem::class); 
    }

    public function journal() 
    { 
        return $this->belongsTo(Journal::class); 
    }

    /**
     * Membuat jurnal akuntansi untuk payroll ini.
     * (Silakan sesuaikan kode akun di bawah dengan COA perusahaan Anda)
     */
    public function generateJournal(): Journal
    {
        $companyId = $this->company_id;

        // Cari ID Akun berdasarkan kode standar atau nama
        // Ganti '5-1000', '1-1000', dll dengan kode COA yang ada di aplikasi Anda
        $expenseAccount = Account::where('company_id', $companyId)->where('code', '5-1000')->first()?->id;
        $cashAccount = Account::where('company_id', $companyId)->where('code', '1-1000')->first()?->id;
        
        // Cari akun utang PPh 21 dan BPJS
        $taxAccount = Account::where('company_id', $companyId)->where('name', 'like', '%PPh 21%')->first()?->id;
        $bpjsAccount = Account::where('company_id', $companyId)->where('name', 'like', '%BPJS%')->first()?->id;

        // Pastikan akun utama ada, jika tidak error
        if (!$expenseAccount || !$cashAccount) {
            throw new \Exception("Akun Beban Gaji atau Kas tidak ditemukan. Periksa COA Anda.");
        }

        $journal = Journal::create([
            'company_id' => $companyId,
            'date' => $this->payment_date ?? now(),
            'reference' => 'PAYROLL-' . $this->payroll_number,
            'description' => 'Jurnal Gaji Periode ' . $this->period_month . '/' . $this->period_year,
            'status' => 'posted',
        ]);

        // 1. Debit: Beban Gaji (Gross)
        $journal->entries()->create([
            'account_id' => $expenseAccount,
            'debit' => $this->total_gross,
            'credit' => 0,
            'description' => 'Beban Gaji Karyawan'
        ]);

        // 2. Kredit: Kas/Bank (Net)
        $journal->entries()->create([
            'account_id' => $cashAccount,
            'debit' => 0,
            'credit' => $this->total_net,
            'description' => 'Pembayaran Gaji Bersih'
        ]);

        // 3. Kredit: Utang PPh 21 (Jika ada)
        if ($this->total_pph21 > 0 && $taxAccount) {
            $journal->entries()->create([
                'account_id' => $taxAccount,
                'debit' => 0,
                'credit' => $this->total_pph21,
                'description' => 'Potongan PPh 21'
            ]);
        }

        // 4. Kredit: Utang BPJS (Jika ada)
        if ($this->total_bpjs > 0 && $bpjsAccount) {
            $journal->entries()->create([
                'account_id' => $bpjsAccount,
                'debit' => 0,
                'credit' => $this->total_bpjs,
                'description' => 'Potongan BPJS'
            ]);
        }

        // Simpan journal_id ke payroll
        $this->update(['journal_id' => $journal->id]);

        return $journal;
    }
}