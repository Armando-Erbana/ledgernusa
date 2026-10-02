@extends('layouts.app')
@section('title', 'Kategori Produk')
@section('content')

<div class="ln-page-head">
    <div><h1 class="ln-page-title">Kategori Produk</h1></div>
    <a href="{{ route('products.index') }}" class="ln-action">← Produk</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="md:col-span-2">
        <div class="ln-table-card">
            <table class="ln-table">
                <thead><tr><th>Nama</th><th>Deskripsi</th><th class="num">Produk</th><th class="center">Aksi</th></tr></thead>
                <tbody>
                @forelse($categories as $c)
                    <tr>
                        <td><strong>{{ $c->name }}</strong></td>
                        <td>{{ $c->description ?: '-' }}</td>
                        <td class="num">{{ $c->products_count }}</td>
                        <td class="center">
                            <form method="POST" action="{{ route('product-categories.destroy', $c) }}" class="inline" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="ln-action red">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">Belum ada kategori</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div>
        <form method="POST" action="{{ route('product-categories.store') }}" class="ln-form-card space-y-3">
            @csrf
            <div class="ln-section-title">Kategori Baru</div>
            <div><label class="ln-label">Nama *</label><input type="text" name="name" required class="ln-input"></div>
            <div><label class="ln-label">Deskripsi</label><textarea name="description" rows="2" class="ln-textarea"></textarea></div>
            <button class="ln-btn-primary w-full justify-center">Tambah</button>
        </form>
    </div>
</div>

@endsection