@extends('layouts.app')
@section('title', 'Jalankan Depresiasi')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Jalankan Depresiasi</h1>
        <p class="ln-page-sub">Proses depresiasi bulanan otomatis untuk semua aset aktif</p>
    </div>
    <a href="{{ route('fixed-assets.index') }}" class="ln-action">← Kembali</a>
</div>

<div class="ln-form-card max-w-2xl">
    <div class="ln-alert ln-alert-success mb-4">
        <strong>Cara kerja:</strong> Sistem akan menghitung depresiasi untuk semua aset berstatus <b>active</b> di company ini. Setiap aset akan mendapat 1 jurnal depresiasi untuk periode yang dipilih.
    </div>

    <form method="POST" action="{{ route('fixed-assets.depreciation-run.store') }}">
        @csrf

        <div class="mb-4">
            <label class="ln-label">Periode Depresiasi *</label>
            <input type="date" name="period" value="{{ $period }}" required class="ln-input">
            <small class="text-gray-500">Pilih tanggal di bulan yang mau didepresiasi. Sistem akan pakai akhir bulan.</small>
        </div>

        <div class="ln-form-actions">
            <button class="ln-btn-primary" onclick="return confirm('Jalankan depresiasi untuk periode ini?')">Jalankan Depresiasi</button>
            <a href="{{ route('fixed-assets.depreciations') }}" class="ln-btn-outline">Lihat Riwayat</a>
        </div>
    </form>
</div>

@endsection