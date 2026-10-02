@extends('layouts.app')
@section('title', 'Mutasi Stok Baru')
@section('content')

<div class="ln-page-head">
    <div><h1 class="ln-page-title">Mutasi Stok Baru</h1></div>
    <a href="{{ route('stock.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('stock.store') }}" class="ln-form-card space-y-4 max-w-2xl">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Jenis *</label>
            <select name="type" id="type" class="ln-select" required onchange="toggleCost()">
                <option value="in">Stok Masuk</option>
                <option value="out">Stok Keluar</option>
                <option value="adjustment">Adjustment (Opname)</option>
            </select>
        </div>
        <div><label class="ln-label">Tanggal *</label><input type="date" name="date" value="{{ date('Y-m-d') }}" required class="ln-input"></div>
    </div>

    <div>
        <label class="ln-label">Produk *</label>
        <select name="product_id" required class="ln-select">
            <option value="">- Pilih Produk -</option>
            @foreach($products as $p)<option value="{{ $p->id }}" @selected(old('product_id') == $p->id)>{{ $p->code }} - {{ $p->name }} (stok: {{ $p->stock }})</option>@endforeach
        </select>
    </div>

    <div>
        <label class="ln-label">Gudang *</label>
        <select name="warehouse_id" required class="ln-select">
            <option value="">- Pilih Gudang -</option>
            @foreach($warehouses as $w)<option value="{{ $w->id }}" @selected(old('warehouse_id') == $w->id)>{{ $w->name }}</option>@endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label" id="qtyLabel">Qty *</label>
            <input type="number" name="qty" value="{{ old('qty') }}" required min="0.0001" step="0.0001" class="ln-input">
        </div>
        <div id="costField">
            <label class="ln-label">Harga Pokok / Unit (Rp)</label>
            <input type="number" name="unit_cost" value="{{ old('unit_cost', 0) }}" min="0" step="0.01" class="ln-input">
        </div>
    </div>

    <div><label class="ln-label">Referensi</label><input type="text" name="reference" value="{{ old('reference') }}" class="ln-input" placeholder="No. bukti, faktur, dll"></div>
    <div><label class="ln-label">Keterangan</label><textarea name="description" rows="2" class="ln-textarea">{{ old('description') }}</textarea></div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Simpan</button>
        <a href="{{ route('stock.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

<script>
function toggleCost() {
    const type = document.getElementById('type').value;
    const costField = document.getElementById('costField');
    const qtyLabel = document.getElementById('qtyLabel');
    if (type === 'in') {
        costField.style.display = '';
        qtyLabel.textContent = 'Qty Masuk *';
    } else if (type === 'out') {
        costField.style.display = 'none';
        qtyLabel.textContent = 'Qty Keluar *';
    } else {
        costField.style.display = 'none';
        qtyLabel.textContent = 'Stok Akhir (Setelah Opname) *';
    }
}
document.addEventListener('DOMContentLoaded', toggleCost);
</script>

@endsection