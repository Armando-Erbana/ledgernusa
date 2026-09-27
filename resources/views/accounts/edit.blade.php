@extends('layouts.app')
@section('title', 'Edit Akun')
@section('content')

<h1 class="text-xl font-bold mb-4">Edit Akun</h1>

<form method="POST" action="{{ route('accounts.update', $account) }}" class="bg-white p-4 rounded shadow space-y-3">
    @csrf @method('PUT')
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm text-gray-600">Kode</label>
            <input type="text" name="code" value="{{ old('code', $account->code) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm text-gray-600">Nama</label>
            <input type="text" name="name" value="{{ old('name', $account->name) }}" required class="w-full border rounded px-3 py-2">
        </div>
    </div>
    <div>
        <label class="block text-sm text-gray-600">Tipe</label>
        <select name="type" required class="w-full border rounded px-3 py-2">
            @foreach(['asset','liability','equity','revenue','expense'] as $t)
                <option value="{{ $t }}" @selected($account->type === $t)>{{ $t }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm text-gray-600">Parent</label>
        <select name="parent_id" class="w-full border rounded px-3 py-2">
            <option value="">- Tidak ada -</option>
            @foreach($parents as $p)
                <option value="{{ $p->id }}" @selected($account->parent_id == $p->id)>{{ $p->code }} - {{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-4">
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_cash" value="1" @checked($account->is_cash)> Kas
        </label>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_bank" value="1" @checked($account->is_bank)> Bank
        </label>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" @checked($account->is_active)> Aktif
        </label>
    </div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Update</button>
    <a href="{{ route('accounts.index') }}" class="px-4 py-2 bg-gray-100 rounded">Batal</a>
</form>

@endsection