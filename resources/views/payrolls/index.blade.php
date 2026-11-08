@extends('layouts.app')
@section('title', 'Payroll')
@section('content')

<style>
/* ===== Hanya tombol: gelap → biru (2-tone: #2563EB, #1E3A8A) ===== */
.ln-btn-primary{
    background:linear-gradient(180deg,#2563EB,#1E3A8A) !important;
    border-color:transparent !important;
    color:#fff !important;
}
.ln-btn-primary:hover{
    background:linear-gradient(180deg,#1E3A8A,#1E3A8A) !important;
}
.ln-btn-outline{
    color:#1E3A8A !important;
    border-color:rgba(37,99,235,.35) !important;
}
.ln-btn-outline:hover{
    background:rgba(37,99,235,.08) !important;
    border-color:rgba(37,99,235,.55) !important;
}
</style>

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Payroll</h1>
        <p class="ln-page-sub">Penggajian karyawan</p>
    </div>
    <a href="{{ route('payrolls.create') }}" class="ln-btn-primary">+ Payroll Baru</a>
</div>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr><th>No. Payroll</th><th>Periode</th><th>Tgl Bayar</th><th class="num">Gross</th><th class="num">PPh 21</th><th class="num">Net</th><th class="center">Status</th><th class="center">Aksi</th></tr>
        </thead>
        <tbody>
        @forelse($payrolls as $p)
            <tr>
                <td><a href="{{ route('payrolls.show', $p) }}" class="ref">{{ $p->payroll_number }}</a></td>
                <td>{{ $p->period_month }}/{{ $p->period_year }}</td>
                <td>{{ $p->payment_date->format('d/m/Y') }}</td>
                <td class="num">Rp {{ number_format($p->total_gross, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($p->total_pph21, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($p->total_net, 0, ',', '.') }}</td>
                <td class="center"><span class="ln-badge {{ $p->status === 'draft' ? 'draft' : 'posted' }}">{{ $p->status }}</span></td>
                <td class="center"><a href="{{ route('payrolls.show', $p) }}" class="ln-action">Lihat</a></td>
            </tr>
        @empty
            <tr><td colspan="8" class="empty">Belum ada payroll</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="ln-pagination">{{ $payrolls->links() }}</div>

@endsection