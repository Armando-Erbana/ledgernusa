@extends('layouts.app')
@section('title', 'Pembelian')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Pembelian</h1>
        <a href="{{ route('export.sales-excel', ['from' => request('from'), 'to' => request('to')]) }}" class="ln-btn-outline">Export CSV</a>
        <p class="ln-page-sub">Daftar bill pembelian</p>
    </div>
    <a href="{{ route('purchases.create') }}" class="ln-btn-primary">+ Pembelian</a>
</div>

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
        @if(request()->has('status'))<a href="{{ route('purchases.index') }}" class="ln-btn-outline">Reset</a>@endif
    </div>
</form>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr><th>Bill</th><th>Tanggal</th><th>Supplier</th><th class="num">Total</th><th class="num">Sisa</th><th class="center">Status</th><th class="center">Aksi</th></tr>
        </thead>
        <tbody>
        @forelse($purchases as $p)
            <tr>
                <td><a href="{{ route('purchases.show', $p) }}" class="ref">{{ $p->bill_number }}</a></td>
                <td class="whitespace-nowrap">{{ $p->date->format('d/m/Y') }}</td>
                <td>{{ $p->supplier->name ?? '-' }}</td>
                <td class="num">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($p->balance, 0, ',', '.') }}</td>
                <td class="center"><span class="ln-badge {{ $p->status === 'paid' ? 'posted' : 'draft' }}">{{ $p->status }}</span></td>
                <td class="center whitespace-nowrap">
                    <a href="{{ route('purchases.show', $p) }}" class="ln-action">Lihat</a>
                    @if($p->status !== 'paid')
                        <span class="ln-action-sep">|</span>
                        <a href="{{ route('purchases.payment.create', $p) }}" class="ln-action green">Bayar</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty">Belum ada pembelian</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="ln-pagination">{{ $purchases->links() }}</div>
@endsection