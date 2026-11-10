<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Support\Facades\DB;

class SalePayment extends Model
{
    use HasFactory, BelongsToCompany, Auditable;

    protected $fillable = [
        'company_id',
        'branch_id',
        'sale_id',
        'date',
        'reference',
        'amount',
        'cash_account_id',
        'notes',
        'created_by',
        'journal_id',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function cashAccount()
    {
        return $this->belongsTo(Account::class, 'cash_account_id');
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function generateJournal(): Journal
    {
        $companyId = $this->company_id;

        // 1. Cari Akun Kas/Bank yang dipilih (pastikan ada)
        $cashAccount = Account::find($this->cash_account_id);
        if (!$cashAccount) {
            throw new \Exception("Akun Kas/Bank yang dipilih tidak valid.");
        }

        // 2. Cari Akun Piutang (AR) yang lebih pintar
        $arAccount = Account::where('company_id', $companyId)
            ->where('type', 'asset')
            ->where(function($q) {
                $q->where('name', 'like', '%piutang%')
                  ->orWhere('name', 'like', '%receivable%')
                  ->orWhere('code', 'like', '102%') // fallback kode umum piutang
                  ->orWhere('code', 'like', '1-2000%'); // fallback kode COA lain
            })
            ->first();

        if (!$arAccount) {
            throw new \Exception("Akun Piutang (Accounts Receivable) tidak ditemukan di COA. Pastikan Anda sudah membuat Akun bertipe 'Aset' dengan nama mengandung kata 'Piutang'.");
        }

        // 3. Buat Jurnal
        $journal = Journal::create([
            'company_id' => $companyId,
            'branch_id' => $this->branch_id ?? session('branch_id'),
            'date' => $this->date,
            'reference' => $this->reference ?: 'PAY-' . $this->id,
            'description' => 'Pembayaran penjualan ' . ($this->sale->invoice_number ?? ''),
            'status' => 'posted',
            'created_by' => $this->created_by,
            'posted_by' => $this->created_by,
            'posted_at' => now(),
        ]);

        // 4. Buat Entri Debit & Kredit
        $journal->entries()->createMany([
            [
                'account_id' => $this->cash_account_id,
                'debit' => $this->amount,
                'credit' => 0,
                'description' => 'Kas diterima',
            ],
            [
                'account_id' => $arAccount->id,
                'debit' => 0,
                'credit' => $this->amount,
                'description' => 'Pelunasan piutang',
            ],
        ]);

        return $journal;
    }
}