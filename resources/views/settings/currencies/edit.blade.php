@extends('layouts.app')

@section('title', 'Edit Currency')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit Currency: {{ $currency->code }}</h1>

<form method="POST" action="{{ route('settings.currencies.update', $currency) }}"
      class="bg-white p-6 rounded shadow max-w-lg">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="block mb-1 font-medium">Nama</label>
        <input type="text" name="name" value="{{ old('name', $currency->name) }}"
               required class="border p-2 rounded w-full">
    </div>

    <div class="mb-3">
        <label class="block mb-1 font-medium">Symbol</label>
        <input type="text" name="symbol" value="{{ old('symbol', $currency->symbol) }}"
               class="border p-2 rounded w-full">
    </div>

    <div class="mb-3">
        <label class="block mb-1 font-medium">Decimal Places</label>
        <input type="number" name="decimal_places" min="0" max="4"
               value="{{ old('decimal_places', $currency->decimal_places) }}"
               required class="border p-2 rounded w-full">
    </div>

    <div class="mb-3">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1"
                   {{ $currency->is_active ? 'checked' : '' }}>
            <span>Aktif</span>
        </label>
    </div>

    <div class="flex gap-3">
        <button class="bg-blue-600 text-white px-6 py-2 rounded">Update</button>
        <a href="{{ route('settings.currencies.index') }}" class="border px-6 py-2 rounded">Batal</a>
    </div>
</form>
@endsection