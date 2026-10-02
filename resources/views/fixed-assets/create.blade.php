@extends('layouts.app')
@section('title', 'Aset Tetap Baru')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Aset Tetap Baru</h1>
        <p class="ln-page-sub">Jurnal perolehan akan otomatis dibuat</p>
    </div>
    <a href="{{ route('fixed-assets.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('fixed-assets.store') }}" class="ln-form-card space-y-4 max-w-3xl">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="ln-label">Kode</label>
            <input type="text" name="code" value="{{ old('code') }}" class="ln-input" placeholder="AST-001">
        </div>
        <div class="md:col-span-2">
            <label class="ln-label">Nama Aset *</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="ln-input" placeholder="Mobil Toyota Avanza">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Kategori</label>
            <input type="text" name="category" value="{{ old('category') }}" class="ln-input" placeholder="kendaraan / bangunan / komputer">
        </div>
        <div>
            <label class="ln-label">Lokasi</label>
            <input type="text" name="location" value="{{ old('location') }}" class="ln-input" placeholder="Kantor Pusat">
        </div>
    </div>

    <div>
        <label class="ln-label">Deskripsi</label>
        <textarea name="description" rows="2" class="ln-textarea">{{ old('description') }}</textarea>
    </div>

    <hr style="border:none; border-top:1px solid var(--ln-line); margin:12px 0;">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Tanggal Perolehan *</label>
            <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="ln-input">
        </div>
        <div>
            <label class="ln-label">Harga Perolehan (Rp) *</label>
            <input type="number" name="purchase_cost" value="{{ old('purchase_cost') }}" required min="0.01" step="0.01" class="ln-input">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Nilai Residu (Rp)</label>
            <input type="number" name="residual_value" value="{{ old('residual_value', 0) }}" min="0" step="0.01" class="ln-input">
        </div>
        <div>
            <label class="ln-label">Umur Ekonomis (bulan) *</label>
            <input type="number" name="useful_life_months" value="{{ old('useful_life_months', 60) }}" required min="1" max="600" class="ln-input">
            <small class="text-gray-500">5 tahun = 60 bulan</small>
        </div>
    </div>

    <div>
        <label class="ln-label">Metode Depresiasi *</label>
        <select name="depreciation_method" class="ln-select" required>
            <option value="straight_line" @selected(old('depreciation_method') === 'straight_line')>Garis Lurus (Straight Line)</option>
            <option value="double_declining" @selected(old('depreciation_method') === 'double_declining')>Saldo Menurun Ganda</option>
            <option value="sum_of_years" @selected(old('depreciation_method') === 'sum_of_years')>Jumlah Angka Tahun</option>
        </select>
    </div>

    <hr style="border:none; border-top:1px solid var(--ln-line); margin:12px 0;">
    <div style="font-size:12.5px; color:#5B6577; font-weight:600; margin-bottom:8px;">Akun Akuntansi</div>

    <div>
        <label class="ln-label">Akun Aset *</label>
        <select name="asset_account_id" required class="ln-select">
            <option value="">- Pilih Akun -</option>
            @foreach($assetAccounts as $a)
                <option value="{{ $a->id }}" @selected(old('asset_account_id') == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="ln-label">Akun Akumulasi Depresiasi *</label>
        <select name="depreciation_account_id" required class="ln-select">
            <option value="">- Pilih Akun -</option>
            @foreach($depreciationAccounts as $a)
                <option value="{{ $a->id }}" @selected(old('depreciation_account_id') == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="ln-label">Akun Beban Depresiasi *</label>
        <select name="expense_account_id" required class="ln-select">
            <option value="">- Pilih Akun -</option>
            @foreach($expenseAccounts as $a)
                <option value="{{ $a->id }}" @selected(old('expense_account_id') == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Simpan Aset</button>
        <a href="{{ route('fixed-assets.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

@endsection