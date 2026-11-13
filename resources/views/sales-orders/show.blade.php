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
        <div class="flex gap-2 items-center mt-1">
            <span class="ln-badge {{ $so->status === 'draft' ? 'draft' : 'posted' }}">{{ $so->status }}</span>
            @if($so->approval_status === 'pending')
                <span class="ln-badge draft">Pending ACC</span>
            @elseif($so->approval_status === 'revision')
                <span class="ln-badge out">Revisi</span>
            @endif
        </div>
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

@if($so->status === 'draft' && $so->approval_status !== 'pending')
    <form method="POST" action="{{ route('sales-orders.confirm', $so) }}" class="mb-4">
        @csrf
        <button class="ln-btn-primary" onclick="return confirm('Konfirmasi SO ini?')">✓ Konfirmasi SO</button>
    </form>
@endif

@if($so->approval_status === 'pending' && auth()->user()->positions->pluck('id')->contains($so->current_approver_position_id))
    <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-xl">
        <h3 class="text-lg font-bold text-blue-900 mb-4">Tindakan Persetujuan</h3>
        <form method="POST" action="{{ route('sales-orders.approve', $so) }}" class="inline">
            @csrf
            <button type="submit" class="ln-btn-primary" onclick="return confirm('Setujui SO ini?')">Setujui (Approve)</button>
        </form>
        
        <form method="POST" action="{{ route('sales-orders.revision', $so) }}" class="inline ml-4">
            @csrf
            <input type="text" name="notes" required placeholder="Alasan revisi..." class="ln-input w-64 inline-block mr-2">
            <button type="submit" class="ln-btn-outline text-red-600 border-red-300">Kembalikan (Revisi)</button>
        </form>
    </div>
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
    <div class="ln-table-card mb-4">
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

@if($so->approvalLogs->count() > 0)
    <h2 class="ln-section-title mt-6">Riwayat Persetujuan</h2>
    <div class="ln-table-card">
        <table class="ln-table">
            <thead>
                <tr><th>Tanggal</th><th>Oleh</th><th>Aksi</th><th>Catatan</th></tr>
            </thead>
            <tbody>
                @foreach($so->approvalLogs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $log->user->name }}</td>
                    <td>
                        @if($log->action === 'approved')
                            <span class="ln-badge posted">Disetujui</span>
                        @else
                            <span class="ln-badge out">Revisi</span>
                        @endif
                    </td>
                    <td>{{ $log->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection