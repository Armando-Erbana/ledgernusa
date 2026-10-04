@extends('layouts.app')
@section('title', 'Detail Payroll')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">{{ $payroll->payroll_number }}</h1>
        <p class="ln-page-sub">Periode {{ $payroll->period_month }}/{{ $payroll->period_year }} · Tgl Bayar {{ $payroll->payment_date->format('d M Y') }}</p>
    </div>
    <a href="{{ route('payrolls.index') }}" class="ln-action">← Kembali</a>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="ln-stat akun"><div class="lbl">Total Gross</div><div class="val">Rp {{ number_format($payroll->total_gross, 0, ',', '.') }}</div></div>
    <div class="ln-stat out"><div class="lbl">Total PPh 21</div><div class="val">Rp {{ number_format($payroll->total_pph21, 0, ',', '.') }}</div></div>
    <div class="ln-stat out"><div class="lbl">Total BPJS</div><div class="val">Rp {{ number_format($payroll->total_bpjs, 0, ',', '.') }}</div></div>
    <div class="ln-stat kas"><div class="lbl">Total Net (Bayar)</div><div class="val">Rp {{ number_format($payroll->total_net, 0, ',', '.') }}</div></div>
</div>

@if($payroll->status === 'draft')
    <form method="POST" action="{{ route('payrolls.post', $payroll) }}" class="mb-4">
        @csrf
        <button class="ln-btn-primary" onclick="return confirm('Posting payroll ini? Jurnal akan otomatis dibuat.')">✓ Posting Payroll</button>
    </form>
@elseif($payroll->status === 'posted')
    <form method="POST" action="{{ route('payrolls.mark-paid', $payroll) }}" class="mb-4">
        @csrf
        <button class="ln-btn-primary" onclick="return confirm('Tandai payroll sudah dibayar?')">💰 Tandai Sudah Dibayar</button>
    </form>
@endif

<h2 class="ln-section-title">Detail Karyawan</h2>
<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Karyawan</th>
                <th class="num">Gaji Pokok</th>
                <th class="num">Tunjangan</th>
                <th class="num">Gross</th>
                <th class="num">PPh 21</th>
                <th class="num">BPJS</th>
                <th class="num">Net</th>
                <th class="center">Slip</th>
            </tr>
        </thead>
        <tbody>
        @foreach($payroll->items as $item)
            <tr>
                <td>{{ $item->employee->name ?? '-' }}</td>
                <td class="num">Rp {{ number_format($item->basic_salary, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($item->allowance, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($item->gross_salary, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($item->pph21, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($item->bpjs_kesehatan + $item->bpjs_ketenagakerjaan, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($item->net_salary, 0, ',', '.') }}</td>
                <td class="center"><a href="{{ route('payrolls.slip', [$payroll, $item]) }}" target="_blank" class="ln-action">Slip</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection