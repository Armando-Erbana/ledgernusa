@extends('layouts.app')
@section('title', 'Jurnal Baru')
@section('content')

<h1 class="text-xl font-bold mb-4">Jurnal Baru</h1>

<form method="POST" action="{{ route('journals.store') }}" class="bg-white p-4 rounded shadow space-y-3" id="journalForm">
    @csrf
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm text-gray-600">Tanggal</label>
            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm text-gray-600">Referensi</label>
            <input type="text" name="reference" value="{{ old('reference') }}" class="w-full border rounded px-3 py-2">
        </div>
    </div>
    <div>
        <label class="block text-sm text-gray-600">Deskripsi</label>
        <textarea name="description" rows="2" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-2 py-2 text-left">Akun</th>
                    <th class="px-2 py-2 text-right w-32">Debit</th>
                    <th class="px-2 py-2 text-right w-32">Kredit</th>
                    <th class="w-10"></th>
                </tr>
            </thead>
            <tbody id="entriesBody"></tbody>
            <tfoot class="bg-gray-50 font-semibold">
                <tr>
                    <td class="px-2 py-2 text-right">Total</td>
                    <td class="px-2 py-2 text-right" id="totalDebit">0</td>
                    <td class="px-2 py-2 text-right" id="totalCredit">0</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <button type="button" onclick="addRow()" class="px-3 py-2 bg-gray-100 rounded text-sm">+ Baris</button>
    <div class="mt-2" id="balanceMsg"></div>

    <div class="pt-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan Draft</button>
        <a href="{{ route('journals.index') }}" class="px-4 py-2 bg-gray-100 rounded">Batal</a>
    </div>
</form>

<script>
const accounts = @json($accounts->map(fn($a) => ['id' => $a->id, 'label' => $a->code . ' - ' . $a->name]));
const body = document.getElementById('entriesBody');

function addRow() {
    const tr = document.createElement('tr');
    tr.className = 'border-t';
    tr.innerHTML = `
        <td class="px-2 py-2">
            <select name="entries[][account_id]" class="w-full border rounded px-2 py-1" required>
                <option value="">- Pilih Akun -</option>
                ${accounts.map(a => `<option value="${a.id}">${a.label}</option>`).join('')}
            </select>
        </td>
        <td class="px-2 py-2"><input type="number" step="0.01" name="entries[][debit]" value="0" class="w-full border rounded px-2 py-1 text-right debit" oninput="calc()"></td>
        <td class="px-2 py-2"><input type="number" step="0.01" name="entries[][credit]" value="0" class="w-full border rounded px-2 py-1 text-right credit" oninput="calc()"></td>
        <td class="px-2 py-2 text-center"><button type="button" onclick="this.closest('tr').remove(); calc()" class="text-red-600">✕</button></td>
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
    if (Math.abs(d - c) < 0.01 && d > 0) {
        msg.innerHTML = '<span class="text-green-700 text-sm">✓ Balance</span>';
    } else {
        msg.innerHTML = '<span class="text-red-600 text-sm">✗ Debit dan Kredit belum balance</span>';
    }
}

addRow(); addRow();
</script>

@endsection