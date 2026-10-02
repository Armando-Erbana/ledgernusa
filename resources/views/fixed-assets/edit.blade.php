@extends('layouts.app')
@section('title', 'Edit Aset')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Edit Aset</h1>
        <p class="ln-page-sub">{{ $asset->name }}</p>
    </div>
    <a href="{{ route('fixed-assets.show', $asset) }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('fixed-assets.update', $asset) }}" class="ln-form-card space-y-4 max-w-3xl">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="ln-label">Kode</label>
            <input type="text" name="code" value="{{ old('code', $asset->code) }}" class="ln-input">
        </div>
        <div class="md:col-span-2">
            <label class="ln-label">Nama Aset *</label>
            <input type="text" name="name" value="{{ old('name', $asset->name) }}" required class="ln-input">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Kategori</label>
            <input type="text" name="category" value="{{ old('category', $asset->category) }}" class="ln-input">
        </div>
        <div>
            <label class="ln-label">Lokasi</label>
            <input type="text" name="location" value="{{ old('location', $asset->location) }}" class="ln-input">
        </div>
    </div>

    <div>
        <label class="ln-label">Deskripsi</label>
        <textarea name="description" rows="2" class="ln-textarea">{{ old('description', $asset->description) }}</textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Tanggal Perolehan</label>
            <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date->format('Y-m-d')) }}" required class="ln-input">
        </div>
        <div>
            <label class="ln-label">Harga Perolehan (Rp)</label>
            <input type="number" name="purchase_cost" value="{{ old('purchase_cost', $asset->purchase_cost) }}" required class="ln-input" {{ $asset->depreciations()->exists() ? 'readonly' : '' }}>
            @if($asset->depreciations()->exists())
                <small class="text-red-600">Tidak bisa diubah karena sudah ada depresiasi.</small>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Nilai Residu</label>
            <input type="number" name="residual_value" value="{{ old('residual_value', $asset->residual_value) }}" min="0" step="0.01" class="ln-input">
        </div>
        <div>
            <label class="ln-label">Umur Ekonomis (bulan)</label>
            <input type="number" name="useful_life_months" value="{{ old('useful_life_months', $asset->useful_life_months) }}" required min="1" class="ln-input">
        </div>
    </div>

    <div>
        <label class="ln-label">Metode Depresiasi</label>
        <select name="depreciation_method" class="ln-select" required>
            @foreach(['straight_line' => 'Garis Lurus','double_declining' => 'Saldo Menurun Ganda','sum_of_years' => 'Jumlah Angka Tahun'] as $k => $v)
                <option value="{{ $k }}" @selected($asset->depreciation_method === $k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="ln-label">Akun Aset</label>
        <select name="asset_account_id" required class="ln-select">
            @foreach($assetAccounts as $a)
                <option value="{{ $a->id }}" @selected($asset->asset_account_id == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="ln-label">Akun Akumulasi Depresiasi</label>
        <select name="depreciation_account_id" required class="ln-select">
            @foreach($depreciationAccounts as $a)
                <option value="{{ $a->id }}" @selected($asset->depreciation_account_id == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="ln-label">Akun Beban Depresiasi</label>
        <select name="expense_account_id" required class="ln-select">
            @foreach($expenseAccounts as $a)
                <option value="{{ $a->id }}" @selected($asset->expense_account_id == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Update Aset</button>
        <a href="{{ route('fixed-assets.show', $asset) }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

@endsection