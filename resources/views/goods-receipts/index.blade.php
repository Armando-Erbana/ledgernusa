@extends('layouts.app')
@section('title', 'Penerimaan Barang')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Penerimaan Barang</h1>
        <p class="ln-page-sub">Bukti barang datang dari supplier</p>
    </div>
    <a href="{{ route('goods-receipts.create') }}" class="ln-btn-primary">+ GR Baru</a>
</div>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr><th>No. GR</th><th>Tanggal</th><th>Supplier</th><th>PO</th><th class="center">Status</th><th class="center">Aksi</th></tr>
        </thead>
        <tbody>
        @forelse($grs as $g)
            <tr>
                <td><a href="{{ route('goods-receipts.show', $g) }}" class="ref">{{ $g->gr_number }}</a></td>
                <td class="whitespace-nowrap">{{ $g->date->format('d/m/Y') }}</td>
                <td>{{ $g->supplier->name ?? '-' }}</td>
                <td>{{ $g->purchaseOrder->po_number ?? '-' }}</td>
                <td class="center"><span class="ln-badge {{ $g->status === 'received' ? 'posted' : 'draft' }}">{{ $g->status }}</span></td>
                <td class="center"><a href="{{ route('goods-receipts.show', $g) }}" class="ln-action">Lihat</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">Belum ada GR</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="ln-pagination">{{ $grs->links() }}</div>

@endsection