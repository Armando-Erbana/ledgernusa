<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #14213A; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #14213A; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #14213A; color: #fff; padding: 6px 8px; text-align: left; }
        th.num { text-align: right; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        td.num { text-align: right; }
        .total { font-weight: bold; background: #f5f5f5; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company->name ?? 'Perusahaan' }}</h1>
        <p>Neraca Saldo</p>
        <p>Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
    </div>

    <table>
        <thead><tr><th>Kode</th><th>Nama Akun</th><th class="num">Debit</th><th class="num">Kredit</th></tr></thead>
        <tbody>
        @foreach($rows as $r)
            <tr>
                <td>{{ $r['account']->code }}</td>
                <td>{{ $r['account']->name }}</td>
                <td class="num">{{ $r['debit'] > 0 ? number_format($r['debit'], 0, ',', '.') : '-' }}</td>
                <td class="num">{{ $r['credit'] > 0 ? number_format($r['credit'], 0, ',', '.') : '-' }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td colspan="2" style="text-align:right">Total</td>
            <td class="num">{{ number_format($totalDebit, 0, ',', '.') }}</td>
            <td class="num">{{ number_format($totalCredit, 0, ',', '.') }}</td>
        </tr>
        </tbody>
    </table>

    <div class="footer">Dicetak dari LedgerNusa · {{ now()->format('d M Y H:i') }}</div>
</body>
</html>