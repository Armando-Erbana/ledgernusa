@extends('layouts.app')

@section('title', 'Exchange Rates')

@section('content')
<h1 class="text-2xl font-bold mb-4">Exchange Rates</h1>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-3">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('settings.exchange-rates.store') }}"
      class="grid grid-cols-5 gap-3 bg-white p-4 rounded shadow mb-6">
    @csrf

    <select name="from_currency" required class="border p-2 rounded">
        @foreach($currencies as $c)
            <option value="{{ $c->code }}">{{ $c->code }}</option>
        @endforeach
    </select>

    <select name="to_currency" required class="border p-2 rounded">
        @foreach($currencies as $c)
            <option value="{{ $c->code }}" @selected($c->code === 'IDR')>{{ $c->code }}</option>
        @endforeach
    </select>

    <input type="number" step="0.00000001" name="rate"
           placeholder="Rate" required class="border p-2 rounded">

    <input type="date" name="effective_date"
           value="{{ date('Y-m-d') }}" required class="border p-2 rounded">

    <button class="bg-blue-600 text-white rounded p-2">Simpan</button>
</form>

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
                <td class="p-2 border font-mono">{{ $r->from_currency }}/{{ $r->to_currency }}</td>
                <td class="p-2 border text-right">{{ number_format($r->rate, 4) }}</td>
                <td class="p-2 border">{{ $r->effective_date->format('Y-m-d') }}</td>
                <td class="p-2 border">{{ $r->source }}</td>
                <td class="p-2 border text-center">
                    <form method="POST"
                          action="{{ route('settings.exchange-rates.destroy', $r) }}"
                          onsubmit="return confirm('Hapus kurs ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="p-4 text-center text-gray-400">Belum ada kurs</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">{{ $rates->links() }}</div>
@endsection