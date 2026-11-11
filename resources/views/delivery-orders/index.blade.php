@extends('layouts.app')
@section('title', 'Surat Jalan (Delivery Order)')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Surat Jalan</h1>
        <p class="ln-page-sub">Daftar pengiriman barang ke customer</p>
    </div>
    <a href="{{ route('delivery-orders.create') }}" class="ln-btn-primary">+ Buat Surat Jalan</a>
</div>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No. DO</th>
                <th>Ref. SO</th>
                <th>Customer</th>
                <th class="center">Status</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dos as $do)
                <tr>
                    <td>{{ $do->date->format('d/m/Y') }}</td>
                    <td class="font-bold">{{ $do->do_number }}</td>
                    <td>{{ $do->salesOrder->so_number }}</td>
                    <td>{{ $do->salesOrder->customer->name ?? '-' }}</td>
                    <td class="center">
                        <span class="ln-badge {{ $do->status === 'delivered' ? 'posted' : 'draft' }}">{{ $do->status }}</span>
                    </td>
                    <td class="center">
                        <a href="{{ route('delivery-orders.show', $do) }}" class="ln-action">Lihat</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Belum ada Surat Jalan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $dos->links() }}</div>

@endsection