@extends('layouts.app')
@section('title', 'Detail GR')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">{{ $gr->gr_number }}</h1>
        <p class="ln-page-sub">{{ $gr->supplier->name ?? '-' }} · {{ $gr->date->format('d M Y') }}</p>
    </div>
    <a href="{{ route('goods-receipts.index') }}" class="ln-action">← Kembali</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
    <div class="ln-form-card"><div class="ln-label">Status</div><span class="ln-badge {{ $gr->status === 'received' ? 'posted' : 'draft' }}">{{ $gr->status }}</span></div>
    <div class="ln-form-card"><div class="ln-label">PO</div><div>{{ $gr->purchaseOrder->po_number ?? '-' }}</div></div>
    <div class="ln-form-card"><div class="ln-label">Diterima Oleh</div><div>{{ $gr->received_by_name ?: '-' }}</div></div>
</div>

@if($gr->status === 'draft')
    <form method="POST" action="{{ route('goods-receipts.receive', $gr) }}" class="mb-4">
        @csrf
        <button class="ln-btn-primary" onclick="return confirm('Tandai barang sudah diterima? Stok akan bertambah.')">📦 Tandai Diterima</button>
    </form>
@elseif($gr->status === 'received' && !$gr->bill_id)
    <form method="POST" action="{{ route('goods-receipts.create-bill', $gr) }}" class="mb-4">
        @csrf
        <button class="ln-btn-primary">📄 Buat Bill</button>
    </form>
@endif

<h2 class="ln-section-title">Item Diterima</h2>
<div class="ln-table-card">
    <table class="ln-table">
        <thead><tr><th>Deskripsi</th><th>Produk</th><th>Gudang</th><th class="num">Qty</th><th class="num">HPP</th></tr></thead>
        <tbody>
        @foreach($gr->items as $i)
            <tr>
                <td>{{ $i->description }}</td>
                <td>{{ $i->product->name ?? '-' }}</td>
                <td>{{ $i->warehouse->name ?? '-' }}</td>
                <td class="num">{{ number_format($i->qty, 2, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($i->unit_cost, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection