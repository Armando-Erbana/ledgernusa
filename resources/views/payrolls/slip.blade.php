<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $item->employee->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #14213A; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #14213A; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 3px 0; font-size: 11px; color: #666; }
        .info { margin-bottom: 20px; }
        .info table { width: 100%; }
        .info td { padding: 3px 0; }
        .info td:first-child { width: 120px; color: #666; }
        table.detail { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.detail th { text-align: left; background: #f5f5f5; padding: 8px; font-size: 11px; }
        table.detail th.num { text-align: right; }
        table.detail td { padding: 6px 8px; border-bottom: 1px solid #eee; }
        table.detail td.num { text-align: right; }
        .total { font-weight: bold; background: #f5f5f5; padding: 10px; margin-top: 10px; }
        .net { font-size: 14px; font-weight: bold; color: #1E7A4C; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $payroll->company->name ?? 'Perusahaan' }}</h1>
        <p>SLIP GAJI KARYAWAN</p>
        <p>Periode {{ $payroll->period_month }}/{{ $payroll->period_year }}</p>
    </div>

    <div class="info">
        <table>
            <tr><td>Nama</td><td><b>{{ $item->employee->name }}</b></td></tr>
            <tr><td>NIK</td><td>{{ $item->employee->employee_number }}</td></tr>
            <tr><td>Jabatan</td><td>{{ $item->employee->position ?: '-' }}</td></tr>
            <tr><td>PTKP</td><td>{{ $item->employee->ptkp_status }}</td></tr>
        </table>
    </div>

    <table class="detail">
        <tr><th>Penghasilan</th><th class="num">Jumlah</th></tr>
        <tr><td>Gaji Pokok</td><td class="num">Rp {{ number_format($item->basic_salary, 0, ',', '.') }}</td></tr>
        <tr><td>Tunjangan</td><td class="num">Rp {{ number_format($item->allowance, 0, ',', '.') }}</td></tr>
        <tr><td>Pendapatan Lain</td><td class="num">Rp {{ number_format($item->other_income, 0, ',', '.') }}</td></tr>
        <tr class="total"><td>Total Bruto</td><td class="num">Rp {{ number_format($item->gross_salary, 0, ',', '.') }}</td></tr>

        <tr><th>Potongan</th><th class="num">Jumlah</th></tr>
        <tr><td>PPh 21</td><td class="num">Rp {{ number_format($item->pph21, 0, ',', '.') }}</td></tr>
        <tr><td>BPJS Kesehatan (1%)</td><td class="num">Rp {{ number_format($item->bpjs_kesehatan, 0, ',', '.') }}</td></tr>
        <tr><td>BPJS Ketenagakerjaan</td><td class="num">Rp {{ number_format($item->bpjs_ketenagakerjaan, 0, ',', '.') }}</td></tr>
        <tr><td>Potongan Lain</td><td class="num">Rp {{ number_format($item->other_deduction, 0, ',', '.') }}</td></tr>
        <tr class="total"><td>Total Potongan</td><td class="num">Rp {{ number_format($item->total_deduction, 0, ',', '.') }}</td></tr>
    </table>

    <div class="total net">
        GAJI BERSIH DITERIMA: Rp {{ number_format($item->net_salary, 0, ',', '.') }}
    </div>

    <div class="footer">
        Dicetak dari LedgerNusa · {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>