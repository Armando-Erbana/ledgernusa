@extends('layouts.app')
@section('title', $product->name)
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">{{ $product->name }}</h1>
        <p class="ln-page-sub">{{ $product->code }} · {{ $product->category->name ?? 'Tanpa kategori' }}</p>
    </div>
    <a href="{{ route('products.index') }}" class="ln-action">← Kembali</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
    <div class="ln-stat akun"><div class="lbl">Stok Saat Ini</div><div class="val">{{ number_format($product->stock, 2, ',', '.') }} {{ $product->unit }}</div></div>
    <div class="ln-stat bank"><div class="lbl">Harga Pokok</div><div class="val">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</div></div>
    <div class="ln-stat kas"><div class="lbl">Harga Jual</div><div class="val">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</div></div>
    <div class="ln-stat jurnal"><div class="lbl">Nilai Persediaan</div><div class="val">Rp {{ number_format($product->stock * $product->cost_price, 0, ',', '.') }}</div></div>
</div>

@php
    $stocksPerWarehouse = \App\Models\ProductStock::where('product_id', $product->id)
        ->with('warehouse')
        ->get();
@endphp

@if($stocksPerWarehouse->count() > 0)
    <h2 class="ln-section-title">Stok per Gudang</h2>
    <div class="ln-table-card mb-4">
        <table class="ln-table">
            <thead>
                <tr>
                    <th>Gudang</th>
                    <th class="num">Stok</th>
                    <th class="center">Status</th>
                </tr>
            </thead>
            <tbody>
            @foreach($stocksPerWarehouse as $ps)
                <tr>
                    <td>{{ $ps->warehouse->name ?? '-' }}</td>
                    <td class="num">{{ number_format($ps->stock, 2, ',', '.') }} {{ $product->unit }}</td>
                    <td class="center">
                        @if($ps->stock <= $ps->min_stock && $ps->min_stock > 0)
                            <span class="ln-badge unpaid">Stok Menipis</span>
                        @else
                            <span class="ln-badge posted">Tersedia</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

<h2 class="ln-section-title">Riwayat Mutasi Stok</h2>
<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr><th>Tanggal</th><th>Gudang</th><th>Jenis</th><th>Ref</th><th class="num">Qty</th><th class="num">HPP</th><th class="num">Stok Setelah</th></tr>
        </thead>
        <tbody>
        @forelse($product->movements()->with('warehouse')->latest('date')->latest('id')->limit(50)->get() as $m)
            <tr>
                <td>{{ $m->date->format('d/m/Y') }}</td>
                <td>{{ $m->warehouse->name ?? '-' }}</td>
                <td><span class="ln-badge {{ $m->type === 'in' ? 'posted' : 'draft' }}">{{ $m->type }}</span></td>
                <td>{{ $m->reference ?: '-' }}</td>
                <td class="num">{{ $m->type === 'out' ? '-' : '+' }}{{ number_format($m->qty, 2, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($m->unit_cost, 0, ',', '.') }}</td>
                <td class="num">{{ number_format($m->stock_after, 2, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty">Belum ada mutasi</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection