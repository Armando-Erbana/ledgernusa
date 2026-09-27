@extends('layouts.app')
@section('title', 'Company Baru')
@section('content')

<h1 class="text-xl font-bold mb-4">Company Baru</h1>

<form method="POST" action="{{ route('companies.store') }}" class="bg-white p-4 rounded shadow space-y-3">
    @csrf
    <div>
        <label class="block text-sm text-gray-600">Nama</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm text-gray-600">Currency</label>
        <input type="text" name="currency" value="{{ old('currency', 'IDR') }}" required class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm text-gray-600">Alamat</label>
        <textarea name="address" class="w-full border rounded px-3 py-2">{{ old('address') }}</textarea>
    </div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
</form>

@endsection