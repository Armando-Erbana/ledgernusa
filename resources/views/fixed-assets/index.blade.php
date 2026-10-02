@extends('layouts.app')
@section('title', 'Aset Tetap')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Aset Tetap</h1>
        <p class="ln-page-sub">Daftar aset perusahaan</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('fixed-assets.depreciation-run') }}" class="ln-btn-outline">Jalankan Depresiasi</a>
        <a href="{{ route('fixed-assets.create') }}" class="ln-btn-primary">+ Aset</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
    <div class="ln-stat akun">
        <div class="lbl">Total Harga Perolehan</div>
        <div class="val">Rp {{ number_format($totalCost, 0, ',', '.') }}</div>
    </div>
    <div class="ln-stat out">
        <div class="lbl">Total Akumulasi Depresiasi</div>
        <div class="val">Rp {{ number_format($totalAccum, 0, ',', '.') }}</div>
    </div>
    <div class="ln-stat kas">
        <div class="lbl">Nilai Buku</div>
        <div class="val">Rp {{ number_format($totalBook, 0, ',', '.') }}</div>
    </div>
</div>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Tgl Beli</th>
                <th class="num">Perolehan</th>
                <th class="num">Nilai Buku</th>
                <th class="center">Status</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($assets as $a)
            <tr>
                <td><span class="ln-code">{{ $a->code ?: '-' }}</span></td>
                <td><strong>{{ $a->name }}</strong></td>
                <td>{{ $a->category ?: '-' }}</td>
                <td class="whitespace-nowrap">{{ $a->purchase_date->format('d/m/Y') }}</td>
                <td class="num">Rp {{ number_format($a->purchase_cost, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($a->book_value, 0, ',', '.') }}</td>
                <td class="center">
                    <span class="ln-badge {{ $a->status === 'active' ? 'posted' : 'draft' }}">{{ $a->status }}</span>
                </td>
                <td class="center whitespace-nowrap">
                    <a href="{{ route('fixed-assets.show', $a) }}" class="ln-action">Lihat</a>
                    @if($a->status === 'active')
                        <span class="ln-action-sep">|</span>
                        <a href="{{ route('fixed-assets.edit', $a) }}" class="ln-action">Edit</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="empty">Belum ada aset tetap</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="ln-pagination">{{ $assets->links() }}</div>

@endsection