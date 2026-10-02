@extends('layouts.app')
@section('title', 'Edit Produk')
@section('content')

<div class="ln-page-head">
    <div><h1 class="ln-page-title">Edit Produk</h1><p class="ln-page-sub">{{ $product->name }}</p></div>
    <a href="{{ route('products.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('products.update', $product) }}" class="ln-form-card space-y-4 max-w-3xl">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="ln-label">Kode *</label><input type="text" name="code" value="{{ old('code', $product->code) }}" required class="ln-input"></div>
        <div class="md:col-span-2"><label class="ln-label">Nama *</label><input type="text" name="name" value="{{ old('name', $product->name) }}" required class="ln-input"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="ln-label">Kategori</label>
            <select name="category_id" class="ln-select">
                <option value="">- Tanpa Kategori -</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected($product->category_id == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="ln-label">Satuan</label><input type="text" name="unit" value="{{ old('unit', $product->unit) }}" required class="ln-input"></div>
        <div><label class="ln-label">Min Stok</label><input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" min="0" step="0.01" class="ln-input"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="ln-label">Harga Pokok</label><input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" class="ln-input" readonly><small class="text-gray-500">Otomatis dari rata-rata pembelian</small></div>
        <div><label class="ln-label">Harga Jual</label><input type="number" name="sell_price" value="{{ old('sell_price', $product->sell_price) }}" min="0" step="0.01" class="ln-input"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="ln-label">Akun Persediaan</label>
            <select name="inventory_account_id" class="ln-select">
                <option value="">-</option>
                @foreach($accounts->where('type', 'asset') as $a)
                    <option value="{{ $a->id }}" @selected($product->inventory_account_id == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="ln-label">Akun Pendapatan</label>
            <select name="sales_account_id" class="ln-select">
                <option value="">-</option>
                @foreach($accounts->where('type', 'revenue') as $a)
                    <option value="{{ $a->id }}" @selected($product->sales_account_id == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="ln-label">Akun HPP</label>
            <select name="cogs_account_id" class="ln-select">
                <option value="">-</option>
                @foreach($accounts->where('type', 'expense') as $a)
                    <option value="{{ $a->id }}" @selected($product->cogs_account_id == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="ln-label">Deskripsi</label>
        <textarea name="description" rows="2" class="ln-textarea">{{ old('description', $product->description) }}</textarea>
    </div>

    <label class="ln-checkbox"><input type="checkbox" name="track_stock" value="1" @checked($product->track_stock)> Track stok</label>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Update</button>
        <a href="{{ route('products.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

@endsection