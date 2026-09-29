@extends('layouts.app')
@section('title', 'Jurnal Baru')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Jurnal Baru</h1>
        <p class="ln-page-sub">Catat transaksi debit dan kredit yang saling seimbang</p>
    </div>
</div>

<form method="POST" action="{{ route('journals.store') }}" class="ln-form-card space-y-4">
    @csrf
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Tanggal</label>
            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="ln-input">
        </div>
        <div>
            <label class="ln-label">Referensi</label>
            <input type="text" name="reference" value="{{ old('reference') }}" class="ln-input">
        </div>
    </div>
    <div>
        <label class="ln-label">Deskripsi</label>
        <textarea name="description" rows="2" class="ln-textarea">{{ old('description') }}</textarea>
    </div>

    <div class="overflow-x-auto rounded-lg border" style="border-color: var(--ln-line);">
        <table class="ln-entries-table">
            <thead>
                <tr>
                    <th>Akun</th>
                    <th class="num" style="width: 8rem;">Debit</th>
                    <th class="num" style="width: 8rem;">Kredit</th>
                    <th style="width: 2.5rem;"></th>
                </tr>
            </thead>
            <tbody id="entriesBody"></tbody>
            <tfoot>
                <tr>
                    <td class="text-right">Total</td>
                    <td class="text-right" id="totalDebit">0</td>
                    <td class="text-right" id="totalCredit">0</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="flex items-center gap-4">
        <button type="button" onclick="addRow()" class="ln-add-row">+ Baris</button>
        <div id="balanceMsg"></div>
    </div>

    <div class="ln-form-actions">
        <button class="ln-btn-primary">Simpan Draft</button>
        <a href="{{ route('journals.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

<script>
const accounts = @json($accounts->map(fn($a) => ['id' => $a->id, 'label' => $a->code . ' - ' . $a->name]));
const body = document.getElementById('entriesBody');

function addRow() {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select name="entries[][account_id]" class="ln-select ln-input-sm" required>
                <option value="">- Pilih Akun -</option>
                ${accounts.map(a => `<option value="${a.id}">${a.label}</option>`).join('')}
            </select>
        </td>
        <td><input type="number" step="0.01" name="entries[][debit]" value="0" class="ln-input ln-input-sm text-right debit" oninput="calc()"></td>
        <td><input type="number" step="0.01" name="entries[][credit]" value="0" class="ln-input ln-input-sm text-right credit" oninput="calc()"></td>
        <td class="text-center"><button type="button" onclick="this.closest('tr').remove(); calc()" class="ln-remove-btn">✕</button></td>
    `;
    body.appendChild(tr);
    calc();
}

function calc() {
    let d = 0, c = 0;
    document.querySelectorAll('.debit').forEach(el => d += parseFloat(el.value) || 0);
    document.querySelectorAll('.credit').forEach(el => c += parseFloat(el.value) || 0);
    document.getElementById('totalDebit').textContent = d.toLocaleString('id-ID');
    document.getElementById('totalCredit').textContent = c.toLocaleString('id-ID');
    const msg = document.getElementById('balanceMsg');
    if (Math.abs(d - c) < 0.01 && d > 0) msg.innerHTML = '<span class="ln-balance-ok">✓ Balance</span>';
    else msg.innerHTML = '<span class="ln-balance-bad">✗ Debit dan Kredit belum balance</span>';
}

addRow(); addRow();
</script>

@endsection