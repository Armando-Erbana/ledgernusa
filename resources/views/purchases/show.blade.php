@extends('layouts.app')
@section('title', 'Detail Pembelian')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">{{ $purchase->bill_number }}</h1>
        <p class="ln-page-sub">{{ $purchase->supplier->name ?? 'Tanpa supplier' }} · {{ $purchase->date->format('d M Y') }}</p>
    </div>
    <a href="{{ route('purchases.index') }}" class="ln-action">← Kembali</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
    <div class="ln-form-card"><div class="ln-label">Status</div><span class="ln-badge {{ $purchase->status === 'paid' ? 'posted' : 'draft' }}">{{ $purchase->status }}</span></div>
    <div class="ln-form-card"><div class="ln-label">Total</div><div style="font-size:20px;font-weight:600;">Rp {{ number_format($purchase->total, 0, ',', '.') }}</div></div>
    <div class="ln-form-card"><div class="ln-label">Sisa</div><div style="font-size:20px;font-weight:600;color:{{ $purchase->balance > 0 ? '#C0392B' : '#1E7A4C' }};">Rp {{ number_format($purchase->balance, 0, ',', '.') }}</div></div>
</div>

<h2 class="ln-section-title">Item</h2>
<div class="ln-table-card mb-4">
    <table class="ln-table">
        <thead><tr><th>Deskripsi</th><th class="num">Qty</th><th class="num">Harga</th><th class="num">Subtotal</th></tr></thead>
        <tbody>
        @foreach($purchase->items as $i)
            <tr>
                <td>{{ $i->description }}<br><small class="text-gray-500">{{ $i->account->name }}</small></td>
                <td class="num">{{ number_format($i->qty, 2, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($i->price, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($i->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="3" class="text-right">Subtotal</td><td class="num">Rp {{ number_format($purchase->subtotal, 0, ',', '.') }}</td></tr>
            @if($purchase->discount > 0)<tr><td colspan="3" class="text-right">Diskon</td><td class="num">- Rp {{ number_format($purchase->discount, 0, ',', '.') }}</td></tr>@endif
            @if($purchase->tax > 0)<tr><td colspan="3" class="text-right">PPN Masukan</td><td class="num">Rp {{ number_format($purchase->tax, 0, ',', '.') }}</td></tr>@endif
            <tr style="background:#f0f4ff"><td colspan="3" class="text-right"><strong>Total</strong></td><td class="num"><strong>Rp {{ number_format($purchase->total, 0, ',', '.') }}</strong></td></tr>
        </tfoot>
    </table>
</div>

@if($purchase->payments->count() > 0)
    <h2 class="ln-section-title">Riwayat Pembayaran</h2>
    <div class="ln-table-card mb-4">
        <table class="ln-table">
            <thead><tr><th>Tanggal</th><th>Akun Kas</th><th class="num">Bayar</th><th class="num">PPh 23</th></tr></thead>
            <tbody>
            @foreach($purchase->payments as $p)
                <tr>
                    <td>{{ $p->date->format('d/m/Y') }}</td>
                    <td>{{ $p->cashAccount->name }}</td>
                    <td class="num">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format($p->pph23, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if($purchase->balance > 0)
    <div class="flex gap-2">
        <a href="{{ route('purchases.payment.create', $purchase) }}" class="ln-btn-primary">Bayar Hutang</a>
        <a href="{{ route('purchases.edit', $purchase) }}" class="ln-btn-outline">Edit</a>
    </div>
@endif
@endsection