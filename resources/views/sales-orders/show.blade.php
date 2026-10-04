@extends('layouts.app')
@section('title', 'Detail SO')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">{{ $so->so_number }}</h1>
        <p class="ln-page-sub">{{ $so->customer->name ?? '-' }} · {{ $so->date->format('d M Y') }}</p>
    </div>
    <a href="{{ route('sales-orders.index') }}" class="ln-action">← Kembali</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
    <div class="ln-form-card">
        <div class="ln-label">Status</div>
        <span class="ln-badge {{ $so->status === 'draft' ? 'draft' : 'posted' }}">{{ $so->status }}</span>
    </div>
    <div class="ln-form-card">
        <div class="ln-label">Total</div>
        <div style="font-size:20px;font-weight:600;">Rp {{ number_format($so->total, 0, ',', '.') }}</div>
    </div>
    <div class="ln-form-card">
        <div class="ln-label">Expected Date</div>
        <div>{{ $so->expected_date?->format('d M Y') ?? '-' }}</div>
    </div>
</div>

@if($so->status === 'draft')
    <form method="POST" action="{{ route('sales-orders.confirm', $so) }}" class="mb-4">
        @csrf
        <button class="ln-btn-primary" onclick="return confirm('Konfirmasi SO ini?')">✓ Konfirmasi SO</button>
    </form>
@endif

<h2 class="ln-section-title">Item Pesanan</h2>
<div class="ln-table-card mb-4">
    <table class="ln-table">
        <thead><tr><th>Produk/Deskripsi</th><th class="num">Qty</th><th class="num">Terkirim</th><th class="num">Harga</th><th class="num">Subtotal</th></tr></thead>
        <tbody>
        @foreach($so->items as $i)
            <tr>
                <td>{{ $i->description }}<br><small class="text-gray-500">{{ $i->product->name ?? '-' }}</small></td>
                <td class="num">{{ number_format($i->qty, 2, ',', '.') }}</td>
                <td class="num">{{ number_format($i->delivered_qty, 2, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($i->price, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($i->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="4" class="text-right">Subtotal</td><td class="num">Rp {{ number_format($so->subtotal, 0, ',', '.') }}</td></tr>
            @if($so->discount > 0)<tr><td colspan="4" class="text-right">Diskon</td><td class="num">- Rp {{ number_format($so->discount, 0, ',', '.') }}</td></tr>@endif
            @if($so->tax > 0)<tr><td colspan="4" class="text-right">PPN</td><td class="num">Rp {{ number_format($so->tax, 0, ',', '.') }}</td></tr>@endif
            <tr style="background:#f0f4ff"><td colspan="4" class="text-right"><strong>Total</strong></td><td class="num"><strong>Rp {{ number_format($so->total, 0, ',', '.') }}</strong></td></tr>
        </tfoot>
    </table>
</div>

@if($so->deliveryOrders->count() > 0)
    <h2 class="ln-section-title">Delivery Order Terkait</h2>
    <div class="ln-table-card">
        <table class="ln-table">
            <thead><tr><th>No. DO</th><th>Tanggal</th><th class="center">Status</th><th class="center">Aksi</th></tr></thead>
            <tbody>
            @foreach($so->deliveryOrders as $do)
                <tr>
                    <td>{{ $do->do_number }}</td>
                    <td>{{ $do->date->format('d/m/Y') }}</td>
                    <td class="center"><span class="ln-badge {{ $do->status === 'delivered' ? 'posted' : 'draft' }}">{{ $do->status }}</span></td>
                    <td class="center"><a href="{{ route('delivery-orders.show', $do) }}" class="ln-action">Lihat</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection