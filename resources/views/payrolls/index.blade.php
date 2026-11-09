@extends('layouts.app')
@section('title', 'Payroll')
@section('content')

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
                <td class="center whitespace-nowrap">
                    <a href="{{ route('payrolls.show', $p) }}" class="ln-action">Lihat</a>
                    
                    {{-- TOMBOL HAPUS --}}
                    @if($p->status !== 'paid')
                        <span class="ln-action-sep">|</span>
                        <form action="{{ route('payrolls.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Hapus payroll ini beserta jurnalnya?')">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="ln-action red">Hapus</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="empty">Belum ada payroll</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="ln-pagination">{{ $payrolls->links() }}</div>

@endsection