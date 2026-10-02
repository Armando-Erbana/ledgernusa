@extends('layouts.app')
@section('title', 'Invoice ' . $sale->invoice_number)
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">{{ $sale->invoice_number }}</h1>
        <p class="ln-page-sub">{{ $sale->customer->name ?? 'Tanpa customer' }} · {{ $sale->date->format('d M Y') }}</p>
    </div>
    <a href="{{ route('sales.index') }}" class="ln-action">← Kembali</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
    <div class="ln-form-card">
        <div class="ln-label">Status</div>
        <span class="ln-badge {{ $sale->status === 'paid' ? 'posted' : 'draft' }}">{{ $sale->status }}</span>
    </div>
    <div class="ln-form-card">
        <div class="ln-label">Total</div>
        <div style="font-size:20px;font-weight:600;">Rp {{ number_format($sale->total, 0, ',', '.') }}</div>
    </div>
    <div class="ln-form-card">
        <div class="ln-label">Sisa Tagihan</div>
        <div style="font-size:20px;font-weight:600;color:{{ $sale->balance > 0 ? '#C0392B' : '#1E7A4C' }};">Rp {{ number_format($sale->balance, 0, ',', '.') }}</div>
    </div>
</div>

<h2 class="ln-section-title">Item</h2>
<div class="ln-table-card mb-4">
    <table class="ln-table">
        <thead><tr><th>Deskripsi</th><th class="num">Qty</th><th class="num">Harga</th><th class="num">Subtotal</th></tr></thead>
        <tbody>
        @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->description }}<br><small class="text-gray-500">{{ $item->account->name }}</small></td>
                <td class="num">{{ number_format($item->qty, 2, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="3" class="text-right">Subtotal</td><td class="num">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td></tr>
            @if($sale->discount > 0)<tr><td colspan="3" class="text-right">Diskon</td><td class="num">- Rp {{ number_format($sale->discount, 0, ',', '.') }}</td></tr>@endif
            @if($sale->tax > 0)<tr><td colspan="3" class="text-right">Pajak</td><td class="num">Rp {{ number_format($sale->tax, 0, ',', '.') }}</td></tr>@endif
            <tr style="background:#f0f4ff"><td colspan="3" class="text-right"><strong>Total</strong></td><td class="num"><strong>Rp {{ number_format($sale->total, 0, ',', '.') }}</strong></td></tr>
        </tfoot>
    </table>
</div>

@if($sale->payments->count() > 0)
    <h2 class="ln-section-title">Riwayat Pembayaran</h2>
    <div class="ln-table-card mb-4">
        <table class="ln-table">
            <thead><tr><th>Tanggal</th><th>Akun Kas</th><th class="num">Jumlah</th></tr></thead>
            <tbody>
            @foreach($sale->payments as $p)
                <tr>
                    <td>{{ $p->date->format('d/m/Y') }}</td>
                    <td>{{ $p->cashAccount->name }}</td>
                    <td class="num">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if($sale->balance > 0)
    <div class="flex gap-2 mt-4">
        <a href="{{ route('sales.payment.create', $sale) }}" class="ln-btn-primary">Catat Pembayaran</a>
        <a href="{{ route('sales.edit', $sale) }}" class="ln-btn-outline">Edit Invoice</a>
    </div>
@endif

@endsection