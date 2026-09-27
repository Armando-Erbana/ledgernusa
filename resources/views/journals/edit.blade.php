@extends('layouts.app')
@section('title', 'Edit Jurnal')
@section('content')

<h1 class="text-xl font-bold mb-4">Edit Jurnal</h1>

<form method="POST" action="{{ route('journals.update', $journal) }}" class="bg-white p-4 rounded shadow space-y-3">
    @csrf @method('PUT')
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm text-gray-600">Tanggal</label>
            <input type="date" name="date" value="{{ old('date', $journal->date->format('Y-m-d')) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm text-gray-600">Referensi</label>
            <input type="text" name="reference" value="{{ old('reference', $journal->reference) }}" class="w-full border rounded px-3 py-2">
        </div>
    </div>
    <div>
        <label class="block text-sm text-gray-600">Deskripsi</label>
        <textarea name="description" rows="2" class="w-full border rounded px-3 py-2">{{ old('description', $journal->description) }}</textarea>
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
        </table>
    </div>

    <button type="button" onclick="addRow()" class="px-3 py-2 bg-gray-100 rounded text-sm">+ Baris</button>

    <div class="pt-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Update</button>
        <a href="{{ route('journals.index') }}" class="px-4 py-2 bg-gray-100 rounded">Batal</a>
    </div>
</form>

<script>
const accounts = @json($accounts->map(fn($a) => ['id' => $a->id, 'label' => $a->code . ' - ' . $a->name]));
const existing = @json($journal->entries->map(fn($e) => ['account_id' => $e->account_id, 'debit' => $e->debit, 'credit' => $e->credit]));
const body = document.getElementById('entriesBody');

function addRow(data = null) {
    const tr = document.createElement('tr');
    tr.className = 'border-t';
    const sel = accounts.map(a => `<option value="${a.id}" ${data && data.account_id == a.id ? 'selected' : ''}>${a.label}</option>`).join('');
    tr.innerHTML = `
        <td class="px-2 py-2">
            <select name="entries[][account_id]" class="w-full border rounded px-2 py-1" required>
                <option value="">- Pilih Akun -</option>${sel}
            </select>
        </td>
        <td class="px-2 py-2"><input type="number" step="0.01" name="entries[][debit]" value="${data ? data.debit : 0}" class="w-full border rounded px-2 py-1 text-right"></td>
        <td class="px-2 py-2"><input type="number" step="0.01" name="entries[][credit]" value="${data ? data.credit : 0}" class="w-full border rounded px-2 py-1 text-right"></td>
        <td class="px-2 py-2 text-center"><button type="button" onclick="this.closest('tr').remove()" class="text-red-600">✕</button></td>
    `;
    body.appendChild(tr);
}

if (existing.length) existing.forEach(e => addRow(e));
else { addRow(); addRow(); }
</script>

@endsection