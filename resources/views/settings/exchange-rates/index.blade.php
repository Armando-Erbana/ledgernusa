@extends('layouts.app')

@section('title', 'Exchange Rates')

@section('content')
<h1 class="text-2xl font-bold mb-4">Exchange Rates</h1>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-3">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 text-red-800 p-3 rounded mb-3">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="bg-red-100 text-red-800 p-3 rounded mb-3">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- Form Manual (Override) --}}
<div class="bg-white p-4 rounded shadow mb-4">
    <h2 class="font-semibold mb-2">Tambah / Override Manual</h2>
    <form method="POST" action="{{ route('settings.exchange-rates.store') }}"
          class="grid grid-cols-2 md:grid-cols-5 gap-3">
        @csrf
        <div>
            <label class="text-xs font-semibold text-gray-500">Dari</label>
            <select name="from_currency" required class="border p-2 rounded w-full">
                @foreach($availableCurrencies as $code => $name)
                    <option value="{{ $code }}" @selected(old('from_currency') === $code)>
                        {{ $code }} — {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-500">Ke</label>
            <select name="to_currency" required class="border p-2 rounded w-full">
                @foreach($availableCurrencies as $code => $name)
                    <option value="{{ $code }}" @selected($code === 'IDR')>
                        {{ $code }} — {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-500">Rate</label>
            <input type="number" step="0.00000001" name="rate" required
                   value="{{ old('rate') }}"
                   class="border p-2 rounded w-full" placeholder="0.00000000">
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-500">Tanggal</label>
            <input type="date" name="effective_date" required
                   value="{{ old('effective_date', date('Y-m-d')) }}"
                   class="border p-2 rounded w-full">
        </div>
        <div class="flex items-end">
            <button class="bg-blue-600 text-white rounded p-2 w-full">Simpan Manual</button>
        </div>
    </form>
</div>

{{-- Form Sync Otomatis --}}
<div class="bg-white p-4 rounded shadow mb-4">
    <h2 class="font-semibold mb-2">Sync dari Frankfurter (Otomatis)</h2>
    <form method="POST" action="{{ route('settings.exchange-rates.sync') }}"
          class="flex flex-wrap items-end gap-2">
        @csrf
        <div>
            <label class="text-xs font-semibold text-gray-500">Dari</label>
            <select name="from_currency" class="border p-2 rounded">
                @foreach($availableCurrencies as $code => $name)
                    <option value="{{ $code }}">{{ $code }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-500">Ke</label>
            <select name="to_currency" class="border p-2 rounded">
                @foreach($availableCurrencies as $code => $name)
                    <option value="{{ $code }}" @selected($code === 'IDR')>{{ $code }}</option>
                @endforeach
            </select>
        </div>
        <button class="bg-indigo-600 text-white rounded p-2">
            🔄 Sync Sekarang
        </button>
    </form>
    <p class="text-xs text-gray-500 mt-2">
        Data diambil dari Frankfurter (sumber: ECB). Update otomatis setiap hari kerja.
    </p>
</div>

{{-- Filter --}}
<div class="bg-white p-4 rounded shadow mb-4">
    <h2 class="font-semibold mb-2">Filter</h2>
    <form method="GET" class="grid grid-cols-2 md:grid-cols-6 gap-2">
        <div>
            <label class="text-xs font-semibold text-gray-500">Dari</label>
            <select name="from_currency" class="border p-2 rounded w-full">
                <option value="">Semua</option>
                @foreach($fromCurrencies as $c)
                    <option value="{{ $c }}" @selected(request('from_currency') === $c)>{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-500">Ke</label>
            <select name="to_currency" class="border p-2 rounded w-full">
                <option value="">Semua</option>
                @foreach($toCurrencies as $c)
                    <option value="{{ $c }}" @selected(request('to_currency') === $c)>{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-500">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="border p-2 rounded w-full">
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-500">Sampai</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="border p-2 rounded w-full">
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-500">Sumber</label>
            <input type="text" name="source" value="{{ request('source') }}"
                   class="border p-2 rounded w-full" placeholder="Frankfurter / Manual">
        </div>
        <div class="flex items-end gap-2">
            <button class="bg-gray-800 text-white rounded p-2 px-4">Filter</button>
            <a href="{{ route('settings.exchange-rates.index') }}"
               class="text-sm text-gray-600 underline">Reset</a>
        </div>
    </form>
</div>

{{-- Tabel --}}
<table class="table-auto w-full border bg-white">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 border text-left">Pair</th>
            <th class="p-2 border text-right">Rate</th>
            <th class="p-2 border text-left">Effective</th>
            <th class="p-2 border text-left">Source</th>
            <th class="p-2 border text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rates as $r)
            <tr class="border-t">
                <td class="p-2 border font-mono">
                    {{ $r->from_currency }}/{{ $r->to_currency }}
                </td>
                <td class="p-2 border text-right">
                    {{ number_format($r->rate, 4, ',', '.') }}
                </td>
                <td class="p-2 border">{{ $r->effective_date->format('Y-m-d') }}</td>
                <td class="p-2 border">{{ $r->source }}</td>
                <td class="p-2 border text-center">
                    <form method="POST"
                          action="{{ route('settings.exchange-rates.destroy', $r) }}"
                          onsubmit="return confirm('Hapus kurs ini?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="p-4 text-center text-gray-400">Belum ada kurs</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">{{ $rates->links() }}</div>
@endsection