@extends('layouts.app')
@section('title', 'Produk Baru')
@section('content')

<div class="ln-page-head">
    <div><h1 class="ln-page-title">Produk Baru</h1></div>
    <a href="{{ route('products.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('products.store') }}" class="ln-form-card space-y-4 max-w-3xl">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="ln-label">Kode *</label><input type="text" name="code" value="{{ old('code') }}" required class="ln-input" placeholder="PRD-001"></div>
        <div class="md:col-span-2"><label class="ln-label">Nama *</label><input type="text" name="name" value="{{ old('name') }}" required class="ln-input"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="ln-label">Kategori</label>
            <select name="category_id" class="ln-select">
                <option value="">- Tanpa Kategori -</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="ln-label">Satuan *</label><input type="text" name="unit" value="{{ old('unit', 'pcs') }}" required class="ln-input"></div>
        <div><label class="ln-label">Min Stok (Alert)</label><input type="number" name="min_stock" value="{{ old('min_stock', 0) }}" min="0" step="0.01" class="ln-input"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="ln-label">Harga Pokok (HPP)</label><input type="number" name="cost_price" value="{{ old('cost_price', 0) }}" min="0" step="0.01" class="ln-input"></div>
        <div><label class="ln-label">Harga Jual</label><input type="number" name="sell_price" value="{{ old('sell_price', 0) }}" min="0" step="0.01" class="ln-input"></div>
    </div>

    <hr style="border:none; border-top:1px solid var(--ln-line); margin:12px 0;">
    <div style="font-size:12.5px; color:#5B6577; font-weight:600;">Akun Akuntansi (opsional)</div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="ln-label">Akun Persediaan</label>
            <select name="inventory_account_id" class="ln-select">
                <option value="">-</option>
                @foreach($accounts->where('type', 'asset') as $a)
                    <option value="{{ $a->id }}" @selected(old('inventory_account_id') == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="ln-label">Akun Pendapatan</label>
            <select name="sales_account_id" class="ln-select">
                <option value="">-</option>
                @foreach($accounts->where('type', 'revenue') as $a)
                    <option value="{{ $a->id }}" @selected(old('sales_account_id') == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="ln-label">Akun HPP</label>
            <select name="cogs_account_id" class="ln-select">
                <option value="">-</option>
                @foreach($accounts->where('type', 'expense') as $a)
                    <option value="{{ $a->id }}" @selected(old('cogs_account_id') == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="ln-label">Deskripsi</label>
        <textarea name="description" rows="2" class="ln-textarea">{{ old('description') }}</textarea>
    </div>

    <label class="ln-checkbox"><input type="checkbox" name="track_stock" value="1" @checked(old('track_stock', true))> Track stok produk ini</label>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Simpan Produk</button>
        <a href="{{ route('products.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

@endsection