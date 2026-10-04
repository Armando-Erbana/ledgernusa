<?php
namespace App\Services;

use App\Models\Employee;

class PayrollService
{
    /**
     * Hitung PPh 21 bulanan (metode disederhanakan).
     * - Biaya jabatan 5% dari bruto, max Rp 500.000/bulan
     * - Iuran pensiun 1% dari bruto
     * - PTKP dibagi 12 bulan
     * - Tarif progresif bulanan
     */
    public function calculatePph21(Employee $employee, float $grossMonthly): float
    {
        // 1. Biaya jabatan: 5% dari bruto, max 500.000/bulan
        $biayaJabatan = min($grossMonthly * 0.05, 500_000);

        // 2. Iuran pensiun 1%
        $iuranPensiun = $grossMonthly * 0.01;

        // 3. Penghasilan netto bulanan
        $nettoMonthly = $grossMonthly - $biayaJabatan - $iuranPensiun;

        // 4. PTKP bulanan
        $ptkpMonthly = $employee->ptkpAmount() / 12;

        // 5. PKP bulanan (Penghasilan Kena Pajak)
        $pkp = max($nettoMonthly - $ptkpMonthly, 0);

        // 6. Tarif progresif bulanan
        //    ≤ 5jt = 5%, > 5jt s.d 16,5jt = 15%, dst.
        $pph = 0;
        if ($pkp > 0) {
            if ($pkp <= 5_000_000) {
                $pph = $pkp * 0.05;
            } elseif ($pkp <= 16_500_000) {
                $pph = 5_000_000 * 0.05 + ($pkp - 5_000_000) * 0.15;
            } elseif ($pkp <= 33_000_000) {
                $pph = 5_000_000 * 0.05 + 11_500_000 * 0.15 + ($pkp - 16_500_000) * 0.25;
            } else {
                $pph = 5_000_000 * 0.05 + 11_500_000 * 0.15 + 16_500_000 * 0.25 + ($pkp - 33_000_000) * 0.30;
            }
        }

        return round($pph, 0);
    }

    /**
     * Hitung BPJS (disederhanakan).
     * - Kesehatan: 1% dari karyawan (maks gaji 12 juta)
     * - JHT: 2% dari karyawan
     * - JP: 1% dari karyawan
     */
    public function calculateBpjs(Employee $employee, float $grossMonthly): array
    {
        $baseKesehatan = min($grossMonthly, 12_000_000);
        $kesehatan = $baseKesehatan * 0.01;
        $jht = $grossMonthly * 0.02;
        $jp = min($grossMonthly, 10_042_300) * 0.01;

        return [
            'kesehatan' => round($kesehatan, 0),
            'ketenagakerjaan' => round($jht + $jp, 0),
        ];
    }

    /**
     * Hitung full payroll item untuk 1 karyawan.
     */
    public function calculateItem(Employee $employee, array $input = []): array
    {
        $basic = $input['basic_salary'] ?? (float) $employee->basic_salary;
        $allowance = $input['allowance'] ?? (float) $employee->fixed_allowance;
        $otherIncome = $input['other_income'] ?? 0;

        $gross = $basic + $allowance + $otherIncome;

        $pph21 = $this->calculatePph21($employee, $gross);
        $bpjs = $this->calculateBpjs($employee, $gross);

        $otherDeduction = $input['other_deduction'] ?? 0;

        $totalDeduction = $pph21 + $bpjs['kesehatan'] + $bpjs['ketenagakerjaan'] + $otherDeduction;
        $netSalary = $gross - $totalDeduction;

        return [
            'employee_id' => $employee->id,
            'basic_salary' => $basic,
            'allowance' => $allowance,
            'other_income' => $otherIncome,
            'gross_salary' => $gross,
            'pph21' => $pph21,
            'bpjs_kesehatan' => $bpjs['kesehatan'],
            'bpjs_ketenagakerjaan' => $bpjs['ketenagakerjaan'],
            'other_deduction' => $otherDeduction,
            'total_deduction' => $totalDeduction,
            'net_salary' => $netSalary,
        ];
    }
}