@extends('layouts.app')
@section('title', 'Edit Invoice')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Edit Invoice</h1>
        <p class="ln-page-sub">{{ $sale->invoice_number }}</p>
    </div>
    <a href="{{ route('sales.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('sales.update', $sale) }}" class="ln-form-card space-y-4">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="ln-label">No. Invoice</label>
            <input type="text" value="{{ $sale->invoice_number }}" class="ln-input" disabled>
        </div>
        <div>
            <label class="ln-label">Tanggal</label>
            <input type="date" name="date" value="{{ old('date', $sale->date->format('Y-m-d')) }}" required class="ln-input">
        </div>
        <div>
            <label class="ln-label">Jatuh Tempo</label>
            <input type="date" name="due_date" value="{{ old('due_date', $sale->due_date?->format('Y-m-d')) }}" class="ln-input">
        </div>
    </div>

    <div>
        <label class="ln-label">Customer</label>
        <select name="customer_id" class="ln-select">
            <option value="">- Tanpa Customer -</option>
            @foreach($customers as $c)
                <option value="{{ $c->id }}" @selected($sale->customer_id == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="ln-label">Item</label>
        <div class="overflow-x-auto">
            <table class="ln-entries-table">
                <thead>
                    <tr>
                        <th>Akun</th>
                        <th>Deskripsi</th>
                        <th class="num" style="width:80px">Qty</th>
                        <th class="num" style="width:130px">Harga</th>
                        <th class="num" style="width:120px">Diskon</th>
                        <th style="width:40px"></th>
                    </tr>
                </thead>
                <tbody id="itemBody"></tbody>
            </table>
        </div>
        <button type="button" onclick="addRow()" class="ln-add-row mt-2">+ Baris</button>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div><label class="ln-label">Diskon Invoice</label><input type="number" name="discount" value="{{ old('discount', $sale->discount) }}" min="0" step="0.01" class="ln-input"></div>
        <div><label class="ln-label">Pajak</label><input type="number" name="tax" value="{{ old('tax', $sale->tax) }}" min="0" step="0.01" class="ln-input"></div>
    </div>

    <div>
        <label class="ln-label">Catatan</label>
        <textarea name="notes" rows="2" class="ln-textarea">{{ old('notes', $sale->notes) }}</textarea>
    </div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Update Invoice</button>
        <a href="{{ route('sales.show', $sale) }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

<script>
const revenueAccounts = @json($revenueAccounts->map(fn($a) => ['id' => $a->id, 'label' => $a->code . ' - ' . $a->name]));
const existing = @json($sale->items->map(fn($i) => ['account_id' => $i->account_id, 'description' => $i->description, 'qty' => $i->qty, 'price' => $i->price, 'discount' => $i->discount]));
const body = document.getElementById('itemBody');

function addRow(data = null) {
    const tr = document.createElement('tr');
    const sel = revenueAccounts.map(a => `<option value="${a.id}" ${data && data.account_id == a.id ? 'selected' : ''}>${a.label}</option>`).join('');
    tr.innerHTML = `
        <td><select name="items[][account_id]" class="ln-select ln-input-sm" required><option value="">- Pilih -</option>${sel}</select></td>
        <td><input type="text" name="items[][description]" value="${data ? data.description : ''}" required class="ln-input ln-input-sm"></td>
        <td><input type="number" name="items[][qty]" value="${data ? data.qty : 1}" min="0.01" step="0.01" class="ln-input ln-input-sm" style="text-align:right"></td>
        <td><input type="number" name="items[][price]" value="${data ? data.price : 0}" min="0" step="0.01" class="ln-input ln-input-sm" style="text-align:right"></td>
        <td><input type="number" name="items[][discount]" value="${data ? data.discount : 0}" min="0" step="0.01" class="ln-input ln-input-sm" style="text-align:right"></td>
        <td class="text-center"><button type="button" onclick="this.closest('tr').remove()" class="ln-remove-btn">✕</button></td>
    `;
    body.appendChild(tr);
}

if (existing.length) existing.forEach(e => addRow(e));
else addRow();
</script>

@endsection