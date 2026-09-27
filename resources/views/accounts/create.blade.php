@extends('layouts.app')
@section('title', 'Akun Baru')
@section('content')

<h1 class="text-xl font-bold mb-4">Akun Baru</h1>

<form method="POST" action="{{ route('accounts.store') }}" class="bg-white p-4 rounded shadow space-y-3">
    @csrf
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm text-gray-600">Kode</label>
            <input type="text" name="code" value="{{ old('code') }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm text-gray-600">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2">
        </div>
    </div>
    <div>
        <label class="block text-sm text-gray-600">Tipe</label>
        <select name="type" required class="w-full border rounded px-3 py-2">
            <option value="asset">Asset</option>
            <option value="liability">Liability</option>
            <option value="equity">Equity</option>
            <option value="revenue">Revenue</option>
            <option value="expense">Expense</option>
        </select>
    </div>
    <div>
        <label class="block text-sm text-gray-600">Parent (opsional)</label>
        <select name="parent_id" class="w-full border rounded px-3 py-2">
            <option value="">- Tidak ada -</option>
            @foreach($parents as $p)
                <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-4">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_cash" value="1"> Kas</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_bank" value="1"> Bank</label>
    </div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
    <a href="{{ route('accounts.index') }}" class="px-4 py-2 bg-gray-100 rounded">Batal</a>
</form>

@endsection