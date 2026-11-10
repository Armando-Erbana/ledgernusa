<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $item->employee->name }}</title>
    <style>
        @page { margin: 40px 50px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #334155; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-muted { color: #64748B; }
        .text-primary { color: #2563EB; }
        
        .header { text-align: center; border-bottom: 2px solid #2563EB; padding-bottom: 20px; margin-bottom: 30px; }
        .company-name { font-size: 22px; font-weight: bold; color: #1E293B; margin: 0; letter-spacing: -0.5px; }
        .doc-title { font-size: 14px; font-weight: bold; color: #64748B; text-transform: uppercase; letter-spacing: 2px; margin-top: 5px; }
        
        /* Layout Info Karyawan Menggunakan 1 Tabel Utuh (Tanpa Nested Table) */
        .info-table { margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 6px 0; vertical-align: top; }
        .info-label { width: 18%; color: #64748B; }
        .info-val { width: 32%; }
        
        .salary-table { margin-top: 10px; border: 1px solid #E2E8F0; border-radius: 8px; overflow: hidden; }
        .salary-table th { background-color: #F8FAFC; color: #475569; font-size: 11px; font-weight: bold; text-transform: uppercase; padding: 12px 16px; border-bottom: 2px solid #E2E8F0; text-align: left; }
        .salary-table td { padding: 10px 16px; border-bottom: 1px solid #F1F5F9; }
        
        .net-salary { margin-top: 20px; padding: 20px; background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 12px; text-align: right; }
        .net-salary .label { font-size: 14px; color: #1E3A8A; font-weight: bold; }
        .net-salary .value { font-size: 24px; color: #2563EB; font-weight: 900; letter-spacing: -1px; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #E2E8F0; padding-top: 15px; margin-top: 30px; }
        .footer td { vertical-align: middle; }
        .footer-text { font-size: 11px; color: #94A3B8; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="company-name">{{ $payroll->company->name ?? 'Nama Perusahaan' }}</h1>
        <div class="doc-title">Slip Gaji Karyawan</div>
    </div>

    <!-- Info Karyawan (Flattened Table) -->
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Karyawan</td>
            <td class="info-val font-bold" style="color: #1E293B;">: {{ $item->employee->name }}</td>
            <td class="info-label">Periode</td>
            <td class="info-val font-bold" style="color: #1E293B;">: {{ $payroll->period_month }}/{{ $payroll->period_year }}</td>
        </tr>
        <tr>
            <td class="info-label">NIK / ID</td>
            <td class="info-val">: {{ $item->employee->employee_number ?? '-' }}</td>
            <td class="info-label">Tanggal Bayar</td>
            <td class="info-val">: {{ $payroll->payment_date->format('d M Y') }}</td>
        </tr>
        <tr>
            <td class="info-label">Jabatan</td>
            <td class="info-val">: {{ $item->employee->position ?? '-' }}</td>
            <td class="info-label">No. Referensi</td>
            <td class="info-val">: {{ $payroll->payroll_number }}</td>
        </tr>
    </table>

    {{-- Tabel Pendapatan --}}
    <table class="salary-table">
        <thead>
            <tr>
                <th>Pendapatan (Earnings)</th>
                <th class="text-right" style="width: 30%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td class="text-right font-bold">Rp {{ number_format($item->basic_salary, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunjangan Tetap</td>
                <td class="text-right font-bold">Rp {{ number_format($item->allowance, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Pendapatan Lain-lain</td>
                <td class="text-right font-bold">Rp {{ number_format($item->other_income, 0, ',', '.') }}</td>
            </tr>
            <tr style="background: #F8FAFC;">
                <td class="font-bold text-primary">Total Bruto (Gross)</td>
                <td class="text-right font-bold text-primary">Rp {{ number_format($item->gross_salary, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Tabel Potongan --}}
    <table class="salary-table" style="margin-top: 15px;">
        <thead>
            <tr>
                <th>Potongan (Deductions)</th>
                <th class="text-right" style="width: 30%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>PPh 21</td>
                <td class="text-right" style="color: #DC2626;">- Rp {{ number_format($item->pph21, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>BPJS Kesehatan (1%)</td>
                <td class="text-right" style="color: #DC2626;">- Rp {{ number_format($item->bpjs_kesehatan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>BPJS Ketenagakerjaan</td>
                <td class="text-right" style="color: #DC2626;">- Rp {{ number_format($item->bpjs_ketenagakerjaan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Potongan Lain-lain</td>
                <td class="text-right" style="color: #DC2626;">- Rp {{ number_format($item->other_deduction, 0, ',', '.') }}</td>
            </tr>
            <tr style="background: #F8FAFC;">
                <td class="font-bold" style="color: #DC2626;">Total Potongan</td>
                <td class="text-right font-bold" style="color: #DC2626;">- Rp {{ number_format($item->total_deduction, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Gaji Bersih --}}
    <div class="net-salary">
        <span class="label">Gaji Bersih (Take Home Pay):</span><br>
        <span class="value">Rp {{ number_format($item->net_salary, 0, ',', '.') }}</span>
    </div>

    <p class="text-muted" style="font-size: 11px; margin-top: 20px;">
        * Slip gaji ini dihasilkan secara otomatis dan bersifat rahasia. Jika ada pertanyaan terkait perhitungan, silakan hubungi bagian HRD/Finance.
    </p>

    {{-- FOOTER BRANDING --}}
    @php
        $logoPath = public_path('images/logo_akutansi_ledger_nusa-removebg-preview.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
    @endphp

    <div class="footer">
        <table>
            <tr>
                <td style="text-align: left;">
                    <div class="footer-text" style="font-weight: bold; color: #64748B; margin-bottom: 2px;">Dokumen Rahasia Perusahaan.</div>
                </td>
                <td style="text-align: right; width: 40%;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="LedgerNusa" style="height: 24px; margin-bottom: 4px;">
                    @endif
                    <div class="footer-text" style="font-size: 10px; text-transform: uppercase;">By Mangata Nusantara Teknologi</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>