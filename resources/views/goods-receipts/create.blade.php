@extends('layouts.app')
@section('title', 'GR Baru')
@section('content')

@php
    $productsJson = \App\Models\Product::where('company_id', session('company_id'))
        ->where('is_active', true)->orderBy('name')
        ->get(['id','code','name','cost_price'])
        ->map(fn($p) => ['id' => $p->id, 'code' => $p->code, 'name' => $p->name, 'cost_price' => (float) $p->cost_price]);

    $warehousesJson = \App\Models\Warehouse::where('company_id', session('company_id'))
        ->where('is_active', true)->orderBy('name')
        ->get(['id','name','is_default'])
        ->map(fn($w) => ['id' => $w->id, 'name' => $w->name, 'is_default' => (bool) $w->is_default]);
@endphp

<div class="ln-page-head">
    <div><h1 class="ln-page-title">Penerimaan Barang Baru</h1></div>
    <a href="{{ route('goods-receipts.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('goods-receipts.store') }}" class="ln-form-card space-y-4">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="ln-label">No. GR *</label><input type="text" name="gr_number" value="{{ old('gr_number', $nextNumber) }}" required class="ln-input"></div>
        <div><label class="ln-label">Tanggal *</label><input type="date" name="date" value="{{ date('Y-m-d') }}" required class="ln-input"></div>
    </div>

    <div>
        <label class="ln-label">Purchase Order (opsional)</label>
        <select name="purchase_order_id" class="ln-select">
            <option value="">- Tanpa PO (GR manual) -</option>
            @foreach($availablePOs as $poItem)
                <option value="{{ $poItem->id }}" @selected($po && $po->id == $poItem->id)>{{ $poItem->po_number }} - {{ $poItem->supplier->name ?? '-' }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="ln-label">Diterima Oleh</label>
        <input type="text" name="received_by_name" class="ln-input" placeholder="Nama penerima">
    </div>

    <div>
        <label class="ln-label">Item Diterima</label>
        <div class="overflow-x-auto">
            <table class="ln-entries-table">
                <thead>
                    <tr><th>Produk</th><th>Deskripsi</th><th>Gudang</th><th class="num" style="width:80px">Qty</th><th class="num" style="width:130px">HPP</th><th style="width:40px"></th></tr>
                </thead>
                <tbody id="grItemBody"></tbody>
            </table>
        </div>
        <button type="button" onclick="addGrRow()" class="ln-add-row mt-2">+ Baris</button>
    </div>

    <div><label class="ln-label">Catatan</label><textarea name="notes" rows="2" class="ln-textarea"></textarea></div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Simpan GR</button>
        <a href="{{ route('goods-receipts.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

<script>
const products = @json($productsJson);
const warehouses = @json($warehousesJson);
const body = document.getElementById('grItemBody');

function addGrRow(data = null) {
    const tr = document.createElement('tr');
    const defaultWarehouseId = warehouses.length > 0
        ? (warehouses.find(w => w.is_default)?.id || warehouses[0].id)
        : '';

    tr.innerHTML = `
        <td>
            <select name="items[][product_id]" class="ln-select ln-input-sm" onchange="onProdGr(this)">
                <option value="">- Non Produk -</option>
                ${products.map(p => `<option value="${p.id}" data-cost="${p.cost_price}" ${data && data.product_id == p.id ? 'selected' : ''}>${p.code} - ${p.name}</option>`).join('')}
            </select>
        </td>
        <td><input type="text" name="items[][description]" value="${data ? data.description : ''}" required class="ln-input ln-input-sm"></td>
        <td>
            <select name="items[][warehouse_id]" class="ln-select ln-input-sm">
                ${warehouses.map(w => `<option value="${w.id}" ${w.id == defaultWarehouseId ? 'selected' : ''}>${w.name}</option>`).join('')}
            </select>
        </td>
        <td><input type="number" name="items[][qty]" value="${data ? data.qty : 1}" min="0.01" step="0.01" class="ln-input ln-input-sm" style="text-align:right"></td>
        <td><input type="number" name="items[][unit_cost]" value="${data ? data.unit_cost : 0}" min="0" step="0.01" class="ln-input ln-input-sm cost" style="text-align:right"></td>
        <td class="text-center"><button type="button" onclick="this.closest('tr').remove()" class="ln-remove-btn">✕</button></td>
    `;
    body.appendChild(tr);
}

function onProdGr(sel) {
    const tr = sel.closest('tr');
    const opt = sel.options[sel.selectedIndex];
    if (opt.dataset.cost) {
        tr.querySelector('.cost').value = opt.dataset.cost;
        const desc = tr.querySelector('input[name="items[][description]"]');
        if (!desc.value) desc.value = opt.text.split(' - ').slice(1).join(' - ');
    }
}

@if($po)
    @foreach($po->items as $poItem)
        addGrRow({
            product_id: {{ $poItem->product_id ?? 'null' }},
            description: @json($poItem->description),
            qty: {{ (float) $poItem->remainingQty() }},
            unit_cost: {{ (float) $poItem->price }}
        });
    @endforeach
@else
    addGrRow();
@endif
</script>

@endsection