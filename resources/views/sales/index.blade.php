@extends('layouts.app')
@section('title', 'Penjualan')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Penjualan</h1>
        <p class="ln-page-sub">Daftar invoice penjualan</p>
    </div>
    <a href="{{ route('sales.create') }}" class="ln-btn-primary">+ Invoice</a>
</div>
<a href="{{ route('export.sales-excel', ['from' => request('from'), 'to' => request('to')]) }}" class="ln-btn-outline">Export CSV</a>
<form method="GET" class="ln-form-card mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
    <div>
        <label class="ln-label">Status</label>
        <select name="status" class="ln-select">
            <option value="">Semua</option>
            <option value="unpaid" @selected(request('status') === 'unpaid')>Belum Bayar</option>
            <option value="partial" @selected(request('status') === 'partial')>Sebagian</option>
            <option value="paid" @selected(request('status') === 'paid')>Lunas</option>
        </select>
    </div>
    <div class="flex items-end md:col-span-2 gap-2">
        <button class="ln-btn-primary">Filter</button>
        @if(request()->has('status'))
            <a href="{{ route('sales.index') }}" class="ln-btn-outline">Reset</a>
        @endif
    </div>
</form>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th class="num">Total</th>
                <th class="num">Sisa</th>
                <th class="center">Status</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($sales as $s)
            <tr>
                <td><a href="{{ route('sales.show', $s) }}" class="ref">{{ $s->invoice_number }}</a></td>
                <td class="whitespace-nowrap">{{ $s->date->format('d/m/Y') }}</td>
                <td>{{ $s->customer->name ?? '-' }}</td>
                <td class="num">Rp {{ number_format($s->total, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($s->balance, 0, ',', '.') }}</td>
                <td class="center">
                    <span class="ln-badge {{ $s->status === 'paid' ? 'posted' : 'draft' }}">{{ $s->status }}</span>
                </td>
                <td class="center whitespace-nowrap">
                    <a href="{{ route('sales.show', $s) }}" class="ln-action">Lihat</a>
                    @if($s->status !== 'paid')
                        <span class="ln-action-sep">|</span>
                        <a href="{{ route('sales.payment.create', $s) }}" class="ln-action green">Bayar</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty">Belum ada invoice</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="ln-pagination">{{ $sales->links() }}</div>

@endsection