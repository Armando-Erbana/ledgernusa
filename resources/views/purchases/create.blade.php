@extends('layouts.app')
@section('title', 'Pembelian Baru')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Pembelian Baru</h1>
        <p class="ln-page-sub">Jurnal otomatis dibuat</p>
    </div>
    <a href="{{ route('purchases.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('purchases.store') }}" class="ln-form-card space-y-4">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="ln-label">No. Bill *</label><input type="text" name="bill_number" value="{{ old('bill_number', $nextNumber) }}" required class="ln-input"></div>
        <div><label class="ln-label">Tanggal *</label><input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="ln-input"></div>
        <div><label class="ln-label">Jatuh Tempo</label><input type="date" name="due_date" value="{{ old('due_date') }}" class="ln-input"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Supplier</label>
            <select name="supplier_id" class="ln-select">
                <option value="">- Tanpa Supplier -</option>
                @foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="ln-label">Tipe Pembayaran *</label>
            <select name="type" class="ln-select" required>
                <option value="credit" @selected(old('type', 'credit') === 'credit')>Kredit (Hutang)</option>
                <option value="cash" @selected(old('type') === 'cash')>Tunai (Kas)</option>
            </select>
        </div>
    </div>

    <div>
        <label class="ln-label">Item Pembelian</label>
        <div class="overflow-x-auto">
            <table class="ln-entries-table">
                <thead>
                    <tr>
                        <th>Akun (Beban/Persediaan)</th>
                        <th>Deskripsi</th>
                        <th class="num" style="width:80px">Qty</th>
                        <th class="num" style="width:130px">Harga</th>
                        <th class="num" style="width:120px">Diskon</th>
                        <th class="num" style="width:130px">Subtotal</th>
                        <th style="width:40px"></th>
                    </tr>
                </thead>
                <tbody id="itemBody"></tbody>
                <tfoot>
                    <tr><td colspan="5" class="text-right">Subtotal</td><td class="text-right" id="fSubtotal">0</td><td></td></tr>
                    <tr><td colspan="5" class="text-right">Diskon Invoice</td><td><input type="number" name="discount" id="fDiscount" value="{{ old('discount', 0) }}" min="0" step="0.01" class="ln-input ln-input-sm" style="text-align:right" oninput="calc()"></td><td></td></tr>
                    @if($company && $company->is_pkp)
                        <tr>
                            <td colspan="5" class="text-right">PPN Masukan ({{ $company->ppn_rate }}%) <input type="hidden" id="ppnRate" value="{{ $company->ppn_rate }}"></td>
                            <td><input type="number" name="tax" id="fTax" value="0" readonly class="ln-input ln-input-sm" style="text-align:right"></td>
                            <td></td>
                        </tr>
                    @else
                        <tr><td colspan="5" class="text-right">Pajak</td><td><input type="number" name="tax" id="fTax" value="{{ old('tax', 0) }}" min="0" step="0.01" class="ln-input ln-input-sm" style="text-align:right" oninput="calc()"></td><td></td></tr>
                    @endif
                    <tr style="background:#f0f4ff"><td colspan="5" class="text-right"><strong>Total</strong></td><td class="text-right"><strong id="fTotal">0</strong></td><td></td></tr>
                </tfoot>
            </table>
        </div>
        <button type="button" onclick="addRow()" class="ln-add-row mt-2">+ Baris</button>
    </div>

    <div><label class="ln-label">Catatan</label><textarea name="notes" rows="2" class="ln-textarea">{{ old('notes') }}</textarea></div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Simpan Pembelian</button>
        <a href="{{ route('purchases.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

<script>
const expAccounts = @json($expenseAccounts->map(fn($a) => ['id' => $a->id, 'label' => $a->code . ' - ' . $a->name]));
const body = document.getElementById('itemBody');

function addRow() {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><select name="items[][account_id]" class="ln-select ln-input-sm" required><option value="">- Pilih -</option>${expAccounts.map(a => `<option value="${a.id}">${a.label}</option>`).join('')}</select></td>
        <td><input type="text" name="items[][description]" required class="ln-input ln-input-sm"></td>
        <td><input type="number" name="items[][qty]" value="1" min="0.01" step="0.01" class="ln-input ln-input-sm qty" style="text-align:right" oninput="calc()"></td>
        <td><input type="number" name="items[][price]" value="0" min="0" step="0.01" class="ln-input ln-input-sm price" style="text-align:right" oninput="calc()"></td>
        <td><input type="number" name="items[][discount]" value="0" min="0" step="0.01" class="ln-input ln-input-sm disc" style="text-align:right" oninput="calc()"></td>
        <td class="text-right subtotal">0</td>
        <td class="text-center"><button type="button" onclick="this.closest('tr').remove(); calc()" class="ln-remove-btn">✕</button></td>
    `;
    body.appendChild(tr); calc();
}

function fmt(n) { return n.toLocaleString('id-ID'); }

function calc() {
    let sub = 0;
    body.querySelectorAll('tr').forEach(tr => {
        const qty = parseFloat(tr.querySelector('.qty')?.value) || 0;
        const price = parseFloat(tr.querySelector('.price')?.value) || 0;
        const disc = parseFloat(tr.querySelector('.disc')?.value) || 0;
        const s = (qty * price) - disc;
        tr.querySelector('.subtotal').textContent = fmt(s);
        sub += s;
    });
    const disc = parseFloat(document.getElementById('fDiscount').value) || 0;
    const base = sub - disc;
    const taxEl = document.getElementById('fTax');
    const rateEl = document.getElementById('ppnRate');
    if (rateEl && taxEl && taxEl.hasAttribute('readonly')) {
        taxEl.value = Math.round(base * (parseFloat(rateEl.value) || 0) / 100);
    }
    const tax = parseFloat(taxEl.value) || 0;
    document.getElementById('fSubtotal').textContent = fmt(sub);
    document.getElementById('fTotal').textContent = fmt(base + tax);
}
addRow();
</script>
@endsection