@extends('layouts.app')
@section('title', 'Mutasi Stok')
@section('content')

<div class="ln-page-head">
    <div><h1 class="ln-page-title">Mutasi Stok</h1><p class="ln-page-sub">Riwayat masuk/keluar barang</p></div>
    <a href="{{ route('stock.create') }}" class="ln-btn-primary">+ Mutasi Stok</a>
</div>

<form method="GET" class="ln-form-card mb-4 grid grid-cols-1 md:grid-cols-5 gap-3">
    <div>
        <label class="ln-label">Produk</label>
        <select name="product_id" class="ln-select">
            <option value="">Semua</option>
            @foreach($products as $p)<option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>{{ $p->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="ln-label">Gudang</label>
        <select name="warehouse_id" class="ln-select">
            <option value="">Semua</option>
            @foreach($warehouses as $w)<option value="{{ $w->id }}" @selected(request('warehouse_id') == $w->id)>{{ $w->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="ln-label">Jenis</label>
        <select name="type" class="ln-select">
            <option value="">Semua</option>
            <option value="in" @selected(request('type') === 'in')>Masuk</option>
            <option value="out" @selected(request('type') === 'out')>Keluar</option>
            <option value="adjustment" @selected(request('type') === 'adjustment')>Adjustment</option>
        </select>
    </div>
    <div><label class="ln-label">Dari</label><input type="date" name="from" value="{{ request('from') }}" class="ln-input"></div>
    <div><label class="ln-label">Sampai</label><input type="date" name="to" value="{{ request('to') }}" class="ln-input"></div>
    <div class="md:col-span-5"><button class="ln-btn-primary">Filter</button></div>
</form>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr><th>Tanggal</th><th>Produk</th><th>Gudang</th><th>Jenis</th><th>Ref</th><th class="num">Qty</th><th class="num">HPP</th><th class="num">Total</th></tr>
        </thead>
        <tbody>
        @forelse($movements as $m)
            <tr>
                <td>{{ $m->date->format('d/m/Y') }}</td>
                <td>{{ $m->product->name ?? '-' }}</td>
                <td>{{ $m->warehouse->name ?? '-' }}</td>
                <td><span class="ln-badge {{ $m->type === 'in' ? 'posted' : 'draft' }}">{{ $m->type }}</span></td>
                <td>{{ $m->reference ?: '-' }}</td>
                <td class="num">{{ $m->type === 'out' ? '-' : '+' }}{{ number_format($m->qty, 2, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($m->unit_cost, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($m->total_cost, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="8" class="empty">Belum ada mutasi stok</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="ln-pagination">{{ $movements->links() }}</div>

@endsection