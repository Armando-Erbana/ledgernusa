<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #14213A; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #14213A; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 4px 0; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #14213A; color: #fff; padding: 8px; text-align: left; font-size: 11px; }
        th.num { text-align: right; }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; }
        td.num { text-align: right; }
        .total { font-weight: bold; background: #f5f5f5; }
        .net { font-weight: bold; font-size: 14px; padding: 10px; }
        .net.positive { color: #1E7A4C; }
        .net.negative { color: #C0392B; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company->name ?? 'Perusahaan' }}</h1>
        <p>Laporan Laba Rugi</p>
        <p>Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
    </div>

    <h3>Pendapatan</h3>
    <table>
        @foreach($revenues as $r)
            <tr>
                <td>{{ $r->code }} - {{ $r->name }}</td>
                <td class="num">Rp {{ number_format($r->period_balance, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr class="total"><td>Total Pendapatan</td><td class="num">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td></tr>
    </table>

    <h3 style="margin-top:20px">Beban</h3>
    <table>
        @foreach($expenses as $e)
            <tr>
                <td>{{ $e->code }} - {{ $e->name }}</td>
                <td class="num">Rp {{ number_format($e->period_balance, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr class="total"><td>Total Beban</td><td class="num">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td></tr>
    </table>

    <div class="net {{ $netIncome >= 0 ? 'positive' : 'negative' }}">
        {{ $netIncome >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}: Rp {{ number_format(abs($netIncome), 0, ',', '.') }}
    </div>

    <div class="footer">Dicetak dari LedgerNusa · {{ now()->format('d M Y H:i') }}</div>
</body>
</html>