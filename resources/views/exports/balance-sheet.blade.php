<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #14213A; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #14213A; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 4px 0; font-size: 11px; color: #666; }
        .two-col { display: flex; gap: 20px; }
        .col { flex: 1; }
        h3 { background: #f5f5f5; padding: 6px 8px; font-size: 13px; margin: 12px 0 6px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        td.num { text-align: right; }
        .total { font-weight: bold; background: #f5f5f5; padding: 8px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company->name ?? 'Perusahaan' }}</h1>
        <p>Neraca</p>
        <p>Per: {{ \Carbon\Carbon::parse($asOf)->format('d M Y') }}</p>
    </div>

    <div class="two-col">
        <div class="col">
            <h3>ASET</h3>
            <table>
                @foreach($assets as $a)
                    <tr><td>{{ $a->code }} - {{ $a->name }}</td><td class="num">Rp {{ number_format($a->balance, 0, ',', '.') }}</td></tr>
                @endforeach
                <tr class="total"><td>Total Aset</td><td class="num">Rp {{ number_format($totalAssets, 0, ',', '.') }}</td></tr>
            </table>
        </div>
        <div class="col">
            <h3>KEWAJIBAN</h3>
            <table>
                @foreach($liabilities as $l)
                    <tr><td>{{ $l->code }} - {{ $l->name }}</td><td class="num">Rp {{ number_format($l->balance, 0, ',', '.') }}</td></tr>
                @endforeach
                <tr class="total"><td>Total Kewajiban</td><td class="num">Rp {{ number_format($totalLiabilities, 0, ',', '.') }}</td></tr>
            </table>

            <h3>EKUITAS</h3>
            <table>
                @foreach($equities as $e)
                    <tr><td>{{ $e->code }} - {{ $e->name }}</td><td class="num">Rp {{ number_format($e->balance, 0, ',', '.') }}</td></tr>
                @endforeach
                <tr class="total"><td>Total Ekuitas</td><td class="num">Rp {{ number_format($totalEquity, 0, ',', '.') }}</td></tr>
            </table>
        </div>
    </div>

    <div class="footer">Dicetak dari LedgerNusa · {{ now()->format('d M Y H:i') }}</div>
</body>
</html>