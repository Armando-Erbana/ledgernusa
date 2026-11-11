@extends('layouts.app')
@section('title', 'Buat Surat Jalan')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Buat Surat Jalan</h1>
        <p class="ln-page-sub">Pilih Sales Order untuk dikirimkan barangnya</p>
    </div>
    <a href="{{ route('delivery-orders.index') }}" class="ln-action">Kembali</a>
</div>

@if(!$so)
<div class="ln-form-card max-w-xl">
    <form method="GET" action="{{ route('delivery-orders.create') }}">
        <label class="ln-label">Pilih Sales Order (Status Confirmed)</label>
        <div class="flex gap-2 mt-2">
            <select name="so_id" class="ln-select" required>
                <option value="">- Pilih SO -</option>
                @foreach($confirmedSOs as $cso)
                    <option value="{{ $cso->id }}">{{ $cso->so_number }} - {{ $cso->customer->name ?? 'Tanpa Customer' }}</option>
                @endforeach
            </select>
            <button type="submit" class="ln-btn-primary">Lanjut</button>
        </div>
    </form>
</div>
@else
<form method="POST" action="{{ route('delivery-orders.store') }}">
    @csrf
    <input type="hidden" name="sales_order_id" value="{{ $so->id }}">
    
    <div class="ln-form-card space-y-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="ln-label">No. SO</label>
                <input type="text" class="ln-input" value="{{ $so->so_number }}" readonly>
            </div>
            <div>
                <label class="ln-label">Customer</label>
                <input type="text" class="ln-input" value="{{ $so->customer->name ?? '-' }}" readonly>
            </div>
            <div>
                <label class="ln-label">Tanggal Kirim *</label>
                <input type="date" name="date" class="ln-input" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>
        <div>
            <label class="ln-label">Catatan</label>
            <input type="text" name="notes" class="ln-input">
        </div>
    </div>

    <div class="ln-form-card mb-4">
        <h3 class="ln-section-title mb-3">Item yang Dikirim</h3>
        <div class="overflow-x-auto">
            <table class="ln-table w-full min-w-[600px]">
                <thead>
                    <tr>
                        <th>Deskripsi</th>
                        <th class="num">Qty Pesanan</th>
                        <th class="num">Sisa Belum Dikirim</th>
                        <th class="num">Qty Kirim *</th>
                        <th>Gudang Asal *</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($so->items as $index => $item)
                        @php $sisa = $item->qty - $item->delivered_qty; @endphp
                        @if($sisa > 0)
                        <tr>
                            <td>
                                {{ $item->description }}
                                <input type="hidden" name="items[{{ $index }}][sales_order_item_id]" value="{{ $item->id }}">
                            </td>
                            <td class="num">{{ number_format($item->qty, 2) }}</td>
                            <td class="num font-bold text-blue-600">{{ number_format($sisa, 2) }}</td>
                            <td>
                                <input type="number" name="items[{{ $index }}][qty]" class="ln-input" value="{{ $sisa }}" min="0.01" max="{{ $sisa }}" step="0.01" required>
                            </td>
                            <td>
                                <select name="items[{{ $index }}][warehouse_id]" class="ln-select" required>
                                    <option value="">- Pilih Gudang -</option>
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="ln-form-actions">
        <button type="submit" class="ln-btn-primary">Proses Pengiriman</button>
        <a href="{{ route('delivery-orders.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>
@endif

@endsection