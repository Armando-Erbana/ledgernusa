@extends('layouts.app')
@section('title', 'Produk')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Produk & Barang</h1>
        <p class="ln-page-sub">{{ $products->total() }} produk terdaftar</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('product-categories.index') }}" class="ln-btn-outline">Kategori</a>
        <a href="{{ route('products.create') }}" class="ln-btn-primary">+ Produk</a>
    </div>
</div>

<form method="GET" class="ln-form-card mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
    <div><label class="ln-label">Cari</label><input type="text" name="q" value="{{ request('q') }}" class="ln-input" placeholder="Nama / kode"></div>
    <div>
        <label class="ln-label">Kategori</label>
        <select name="category_id" class="ln-select">
            <option value="">Semua</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex items-end">
        <label class="ln-checkbox"><input type="checkbox" name="low_stock" value="1" @checked(request('low_stock'))> Stok menipis</label>
    </div>
    <div class="flex items-end gap-2"><button class="ln-btn-primary">Filter</button></div>
</form>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th class="num">HPP</th>
                <th class="num">Harga Jual</th>
                <th class="num">Stok</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($products as $p)
            <tr>
                <td><span class="ln-code">{{ $p->code }}</span></td>
                <td>
                    <strong>{{ $p->name }}</strong>
                    @if($p->isLowStock())
                        <span class="ln-badge draft" style="margin-left:6px;">Stok Menipis</span>
                    @endif
                </td>
                <td>{{ $p->category->name ?? '-' }}</td>
                <td class="num">Rp {{ number_format($p->cost_price, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($p->sell_price, 0, ',', '.') }}</td>
                <td class="num">
                    {{ number_format($p->stock, 2, ',', '.') }} {{ $p->unit }}
                </td>
                <td class="center whitespace-nowrap">
                    <a href="{{ route('products.show', $p) }}" class="ln-action">Lihat</a>
                    <span class="ln-action-sep">|</span>
                    <a href="{{ route('products.edit', $p) }}" class="ln-action">Edit</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty">Belum ada produk</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="ln-pagination">{{ $products->links() }}</div>

@endsection