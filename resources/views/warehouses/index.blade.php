@extends('layouts.app')
@section('title', 'Gudang')
@section('content')

<div class="ln-page-head">
    <div><h1 class="ln-page-title">Gudang</h1><p class="ln-page-sub">Daftar gudang perusahaan</p></div>
    <a href="{{ route('products.index') }}" class="ln-action">← Produk</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="md:col-span-2">
        <div class="ln-table-card">
            <table class="ln-table">
                <thead><tr><th>Kode</th><th>Nama</th><th>Alamat</th><th class="center">Default</th><th class="center">Aksi</th></tr></thead>
                <tbody>
                @forelse($warehouses as $w)
                    <tr>
                        <td><span class="ln-code">{{ $w->code }}</span></td>
                        <td><strong>{{ $w->name }}</strong></td>
                        <td>{{ $w->address ?: '-' }}</td>
                        <td class="center">
                            @if($w->is_default)<span class="ln-badge posted">Default</span>@endif
                        </td>
                        <td class="center">
                            <form method="POST" action="{{ route('warehouses.destroy', $w) }}" class="inline" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="ln-action red">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty">Belum ada gudang</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div>
        <form method="POST" action="{{ route('warehouses.store') }}" class="ln-form-card space-y-3">
            @csrf
            <div class="ln-section-title">Gudang Baru</div>
            <div><label class="ln-label">Kode *</label><input type="text" name="code" required class="ln-input" placeholder="GDG-02"></div>
            <div><label class="ln-label">Nama *</label><input type="text" name="name" required class="ln-input"></div>
            <div><label class="ln-label">Alamat</label><textarea name="address" rows="2" class="ln-textarea"></textarea></div>
            <label class="ln-checkbox"><input type="checkbox" name="is_default" value="1"> Jadikan default</label>
            <button class="ln-btn-primary w-full justify-center">Tambah</button>
        </form>
    </div>
</div>

@endsection