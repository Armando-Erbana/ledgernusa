@extends('layouts.app')

@section('title', 'Tambah Currency')

@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Currency</h1>

<form method="POST" action="{{ route('settings.currencies.store') }}"
      class="bg-white p-6 rounded shadow max-w-lg">
    @csrf

    <div class="mb-3">
        <label class="block mb-1 font-medium">Kode (3 huruf)</label>
        <input type="text" name="code" maxlength="3" value="{{ old('code') }}"
               required class="border p-2 rounded w-full uppercase">
    </div>

    <div class="mb-3">
        <label class="block mb-1 font-medium">Nama</label>
        <input type="text" name="name" value="{{ old('name') }}"
               required class="border p-2 rounded w-full">
    </div>

    <div class="mb-3">
        <label class="block mb-1 font-medium">Symbol</label>
        <input type="text" name="symbol" value="{{ old('symbol') }}"
               class="border p-2 rounded w-full">
    </div>

    <div class="mb-3">
        <label class="block mb-1 font-medium">Decimal Places</label>
        <input type="number" name="decimal_places" min="0" max="4"
               value="{{ old('decimal_places', 2) }}"
               required class="border p-2 rounded w-full">
    </div>

    <div class="flex gap-3">
        <button class="bg-blue-600 text-white px-6 py-2 rounded">Simpan</button>
        <a href="{{ route('settings.currencies.index') }}" class="border px-6 py-2 rounded">Batal</a>
    </div>
</form>
@endsection