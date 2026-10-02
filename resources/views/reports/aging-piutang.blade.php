@extends('layouts.app')
@section('title', 'Aging Piutang')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Aging Piutang</h1>
        <p class="ln-page-sub">Analisis umur piutang pelanggan</p>
    </div>
    <a href="{{ route('reports.index') }}" class="ln-action">← Laporan</a>
</div>

<form method="GET" class="ln-form-card mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
    <div><label class="ln-label">Per Tanggal</label><input type="date" name="as_of" value="{{ $asOf }}" class="ln-input"></div>
    <div class="flex items-end md:col-span-2"><button class="ln-btn-primary">Tampilkan</button></div>
</form>

<div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
    <div class="ln-stat kas"><div class="lbl">Lancar</div><div class="val">Rp {{ number_format($totals['current'], 0, ',', '.') }}</div></div>
    <div class="ln-stat akun"><div class="lbl">1-30 hari</div><div class="val">Rp {{ number_format($totals['d1_30'], 0, ',', '.') }}</div></div>
    <div class="ln-stat akun"><div class="lbl">31-60 hari</div><div class="val">Rp {{ number_format($totals['d31_60'], 0, ',', '.') }}</div></div>
    <div class="ln-stat out"><div class="lbl">61-90 hari</div><div class="val">Rp {{ number_format($totals['d61_90'], 0, ',', '.') }}</div></div>
    <div class="ln-stat out"><div class="lbl">> 90 hari</div><div class="val">Rp {{ number_format($totals['d90_plus'], 0, ',', '.') }}</div></div>
</div>

<div class="ln-stat jurnal mb-4">
    <div class="lbl">Total Piutang</div>
    <div class="val" style="font-size:26px">Rp {{ number_format($totals['grand'], 0, ',', '.') }}</div>
</div>

@foreach(['current' => 'Lancar (Belum Jatuh Tempo)', 'd1_30' => '1-30 Hari', 'd31_60' => '31-60 Hari', 'd61_90' => '61-90 Hari', 'd90_plus' => 'Lebih dari 90 Hari'] as $key => $label)
    @if(count($buckets[$key]) > 0)
        <h2 class="ln-section-title">{{ $label }} — Rp {{ number_format($totals[$key], 0, ',', '.') }}</h2>
        <div class="ln-table-card mb-4">
            <table class="ln-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Jatuh Tempo</th>
                        <th class="num">Total</th>
                        <th class="num">Dibayar</th>
                        <th class="num">Sisa</th>
                        <th class="center">Hari</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($buckets[$key] as $row)
                    <tr>
                        <td><a href="{{ route('sales.show', $row['sale']) }}" class="ref">{{ $row['sale']->invoice_number }}</a></td>
                        <td>{{ $row['sale']->date->format('d/m/Y') }}</td>
                        <td>{{ $row['sale']->customer->name ?? '-' }}</td>
                        <td>{{ $row['sale']->due_date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="num">Rp {{ number_format($row['sale']->total, 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($row['sale']->paid_amount, 0, ',', '.') }}</td>
                        <td class="num" style="font-weight:600">Rp {{ number_format($row['balance'], 0, ',', '.') }}</td>
                        <td class="center">{{ $row['days'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endforeach

@endsection