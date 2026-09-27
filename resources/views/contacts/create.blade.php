@extends('layouts.app')
@section('title', 'Kontak Baru')
@section('content')

<h1 class="text-xl font-bold mb-4">Kontak Baru</h1>

<form method="POST" action="{{ route('contacts.store') }}" class="bg-white p-4 rounded shadow space-y-3">
    @csrf
    <div>
        <label class="block text-sm text-gray-600">Tipe</label>
        <select name="type" class="w-full border rounded px-3 py-2">
            @foreach(['customer','supplier','employee','other'] as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm text-gray-600">Nama</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2">
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm text-gray-600">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm text-gray-600">Telepon</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2">
        </div>
    </div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
    <a href="{{ route('contacts.index') }}" class="px-4 py-2 bg-gray-100 rounded">Batal</a>
</form>

@endsection