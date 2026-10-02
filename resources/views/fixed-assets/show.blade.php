@extends('layouts.app')
@section('title', 'Detail Aset')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">{{ $asset->name }}</h1>
        <p class="ln-page-sub">{{ $asset->code ?: '-' }} · {{ $asset->category ?: 'Tanpa kategori' }}</p>
    </div>
    <a href="{{ route('fixed-assets.index') }}" class="ln-action">← Kembali</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
    <div class="ln-stat akun">
        <div class="lbl">Harga Perolehan</div>
        <div class="val">Rp {{ number_format($asset->purchase_cost, 0, ',', '.') }}</div>
    </div>
    <div class="ln-stat out">
        <div class="lbl">Akumulasi Depresiasi</div>
        <div class="val">Rp {{ number_format($asset->accumulated_depreciation, 0, ',', '.') }}</div>
    </div>
    <div class="ln-stat kas">
        <div class="lbl">Nilai Buku</div>
        <div class="val">Rp {{ number_format($asset->book_value, 0, ',', '.') }}</div>
    </div>
</div>

<div class="ln-form-card mb-4">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
        <div><div class="ln-label">Status</div><span class="ln-badge {{ $asset->status === 'active' ? 'posted' : 'draft' }}">{{ $asset->status }}</span></div>
        <div><div class="ln-label">Tanggal Perolehan</div><div>{{ $asset->purchase_date->format('d M Y') }}</div></div>
        <div><div class="ln-label">Umur Ekonomis</div><div>{{ $asset->useful_life_months }} bulan ({{ round($asset->useful_life_months/12, 1) }} tahun)</div></div>
        <div><div class="ln-label">Nilai Residu</div><div>Rp {{ number_format($asset->residual_value, 0, ',', '.') }}</div></div>
        <div><div class="ln-label">Depresiasi / Bulan</div><div>Rp {{ number_format($asset->monthlyDepreciation(), 0, ',', '.') }}</div></div>
        <div><div class="ln-label">Metode</div><div>{{ str_replace('_', ' ', $asset->depreciation_method) }}</div></div>
        <div><div class="ln-label">Akun Aset</div><div>{{ $asset->assetAccount->code }} - {{ $asset->assetAccount->name }}</div></div>
        <div><div class="ln-label">Akun Akumulasi</div><div>{{ $asset->depreciationAccount->code }} - {{ $asset->depreciationAccount->name }}</div></div>
        <div><div class="ln-label">Akun Beban</div><div>{{ $asset->expenseAccount->code }} - {{ $asset->expenseAccount->name }}</div></div>
        @if($asset->location)<div class="col-span-2 md:col-span-3"><div class="ln-label">Lokasi</div><div>{{ $asset->location }}</div></div>@endif
    </div>
</div>

<h2 class="ln-section-title">Riwayat Depresiasi ({{ $asset->depreciations->count() }} bulan)</h2>
<div class="ln-table-card mb-4">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Periode</th>
                <th class="num">Jumlah</th>
                <th class="num">Akumulasi</th>
                <th class="num">Nilai Buku</th>
            </tr>
        </thead>
        <tbody>
        @forelse($asset->depreciations as $d)
            <tr>
                <td>{{ $d->period_date->format('M Y') }}</td>
                <td class="num">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($d->accumulated_after, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($d->book_value_after, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty">Belum ada depresiasi. Jalankan dari menu Aset Tetap.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@if($asset->status === 'active')
    <form method="POST" action="{{ route('fixed-assets.dispose', $asset) }}" class="ln-form-card max-w-2xl" onsubmit="return confirm('Lepas aset ini?')">
        @csrf
        <h3 class="ln-section-title">Lepas Aset (Jual / Buang)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
            <div>
                <label class="ln-label">Tanggal Pelepasan</label>
                <input type="date" name="disposed_at" value="{{ date('Y-m-d') }}" required class="ln-input">
            </div>
            <div>
                <label class="ln-label">Nilai Jual (Rp)</label>
                <input type="number" name="disposal_value" value="0" min="0" step="0.01" required class="ln-input">
                <small class="text-gray-500">Isi 0 jika aset dibuang</small>
            </div>
        </div>
        <button class="ln-btn-cancel" style="color:#C0392B; border-color:#C0392B;">Lepas Aset</button>
    </form>
@endif

@endsection