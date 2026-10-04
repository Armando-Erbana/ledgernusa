<?php
namespace App\Services;

use App\Models\BankStatement;
use App\Models\BankStatementLine;
use App\Models\CashTransaction;
use App\Models\PurchasePayment;
use App\Models\SalePayment;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class BankReconciliationService
{
    /**
     * Parse CSV mutasi bank.
     *
     * Format CSV (fleksibel, dengan header):
     * date,description,reference,debit,credit,balance
     * 2026-01-15,Transfer masuk,INV-001,1000000,,5000000
     * 2026-01-16,Pembayaran supplier,BILL-001,,500000,4500000
     */
    public function parseCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $rows = [];

        // Normalize header
        $headerMap = [];
        foreach ($header as $i => $col) {
            $headerMap[strtolower(trim($col))] = $i;
        }

        // Cari index kolom
        $dateIdx = $this->findColumn($headerMap, ['date', 'tanggal', 'tgl']);
        $descIdx = $this->findColumn($headerMap, ['description', 'deskripsi', 'keterangan', 'uraian']);
        $refIdx = $this->findColumn($headerMap, ['reference', 'ref', 'no_ref', 'nomor']);
        $debitIdx = $this->findColumn($headerMap, ['debit', 'masuk', 'kredit_masuk', 'in']);
        $creditIdx = $this->findColumn($headerMap, ['credit', 'keluar', 'debit_keluar', 'out']);
        $balanceIdx = $this->findColumn($headerMap, ['balance', 'saldo']);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) continue;
            if (!isset($row[$dateIdx]) || !$row[$dateIdx]) continue;

            $rows[] = [
                'date' => $this->parseDate($row[$dateIdx]),
                'description' => trim($row[$descIdx] ?? '-'),
                'reference' => $refIdx !== null ? trim($row[$refIdx] ?? '') : null,
                'debit' => $debitIdx !== null ? $this->parseNumber($row[$debitIdx] ?? 0) : 0,
                'credit' => $creditIdx !== null ? $this->parseNumber($row[$creditIdx] ?? 0) : 0,
                'balance' => $balanceIdx !== null ? $this->parseNumber($row[$balanceIdx] ?? 0) : null,
            ];
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Cocokkan otomatis setiap line dengan transaksi sistem.
     */
    public function autoMatch(BankStatement $statement): array
    {
        $matched = 0;
        $unmatched = 0;

        foreach ($statement->lines as $line) {
            $match = $this->findMatch($statement, $line);
            if ($match) {
                $line->update([
                    'match_status' => 'matched',
                    'matched_type' => $match['type'],
                    'matched_id' => $match['id'],
                    'matched_at' => now(),
                    'matched_by' => auth()->id(),
                ]);
                $matched++;
            } else {
                $unmatched++;
            }
        }

        return ['matched' => $matched, 'unmatched' => $unmatched];
    }

    /**
     * Cari transaksi yang cocok dengan nominal + tanggal ±3 hari.
     */
    private function findMatch(BankStatement $statement, BankStatementLine $line): ?array
    {
        $companyId = $statement->company_id;
        $amount = max((float) $line->debit, (float) $line->credit);
        $isIncome = (float) $line->debit > 0;

        if ($amount <= 0) return null;

        $dateFrom = Carbon::parse($line->date)->subDays(3);
        $dateTo = Carbon::parse($line->date)->addDays(3);
        $tolerance = $amount * 0.01; // 1% toleransi

        if ($isIncome) {
            // Uang masuk → cocokkan dengan cash IN atau sale payment
            $cash = CashTransaction::where('company_id', $companyId)
                ->where('type', 'in')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->whereBetween('amount', [$amount - $tolerance, $amount + $tolerance])
                ->whereDoesntHave('bankStatementLineMatch')
                ->first();

            if ($cash) return ['type' => 'cash_transaction', 'id' => $cash->id];

            $salePayment = SalePayment::where('company_id', $companyId)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->whereBetween('amount', [$amount - $tolerance, $amount + $tolerance])
                ->first();

            if ($salePayment) return ['type' => 'sale_payment', 'id' => $salePayment->id];
        } else {
            // Uang keluar → cocokkan dengan cash OUT atau purchase payment
            $cash = CashTransaction::where('company_id', $companyId)
                ->where('type', 'out')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->whereBetween('amount', [$amount - $tolerance, $amount + $tolerance])
                ->first();

            if ($cash) return ['type' => 'cash_transaction', 'id' => $cash->id];

            $purchasePayment = PurchasePayment::where('company_id', $companyId)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->whereBetween('amount', [$amount - $tolerance, $amount + $tolerance])
                ->first();

            if ($purchasePayment) return ['type' => 'purchase_payment', 'id' => $purchasePayment->id];
        }

        return null;
    }

    private function findColumn(array $headerMap, array $aliases): ?int
    {
        foreach ($aliases as $alias) {
            if (isset($headerMap[$alias])) return $headerMap[$alias];
        }
        return null;
    }

    private function parseDate(string $date): string
    {
        $date = trim($date);
        // Support format: 2026-01-15, 15/01/2026, 15-01-2026
        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d', 'm/d/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $date)->format('Y-m-d');
            } catch (\Exception $e) { continue; }
        }
        return now()->format('Y-m-d');
    }

    private function parseNumber($num): float
    {
        if (is_numeric($num)) return (float) $num;
        $clean = preg_replace('/[^\d,.-]/', '', (string) $num);
        $clean = str_replace(',', '.', str_replace('.', '', $clean));
        return (float) $clean;
    }
}