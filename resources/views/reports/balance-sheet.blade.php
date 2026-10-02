@extends('layouts.app')
@section('title', 'Neraca')
@section('content')

<h1 class="text-xl font-bold mb-4">Neraca</h1>
<a href="{{ route('export.balance-sheet-pdf', ['as_of' => $asOf]) }}" target="_blank" class="ln-btn-outline">
    📄 Export PDF
</a>

<form method="GET" class="bg-white p-4 rounded shadow mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
    <div>
        <label class="block text-xs text-gray-600">Per Tanggal</label>
        <input type="date" name="as_of" value="{{ $asOf }}" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div class="flex items-end">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded text-sm">Tampilkan</button>
    </div>
</form>

<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    {{-- ASET --}}
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold text-gray-700 mb-2">Aset</h2>
        @forelse($assets as $a)
            <div class="flex justify-between py-1 text-sm border-b">
                <span>{{ $a->code }} - {{ $a->name }}</span>
                <span>Rp {{ number_format($a->balance, 0, ',', '.') }}</span>
            </div>
        @empty
            <div class="text-gray-400 text-sm">-</div>
        @endforelse
        <div class="flex justify-between py-2 font-semibold border-t-2 mt-2">
            <span>Total Aset</span>
            <span>Rp {{ number_format($totalAssets, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- KEWAJIBAN + EKUITAS --}}
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold text-gray-700 mb-2">Kewajiban</h2>
        @forelse($liabilities as $l)
            <div class="flex justify-between py-1 text-sm border-b">
                <span>{{ $l->code }} - {{ $l->name }}</span>
                <span>Rp {{ number_format($l->balance, 0, ',', '.') }}</span>
            </div>
        @empty
            <div class="text-gray-400 text-sm">-</div>
        @endforelse
        <div class="flex justify-between py-2 font-semibold border-t-2 mt-2">
            <span>Total Kewajiban</span>
            <span>Rp {{ number_format($totalLiabilities, 0, ',', '.') }}</span>
        </div>

        <h2 class="font-semibold text-gray-700 mb-2 mt-6">Ekuitas</h2>
        @forelse($equities as $e)
            <div class="flex justify-between py-1 text-sm border-b">
                <span>{{ $e->code }} - {{ $e->name }}</span>
                <span>Rp {{ number_format($e->balance, 0, ',', '.') }}</span>
            </div>
        @empty
            <div class="text-gray-400 text-sm">-</div>
        @endforelse
        <div class="flex justify-between py-1 text-sm border-b">
            <span>Laba Berjalan</span>
            <span>Rp {{ number_format($currentEarnings, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between py-2 font-semibold border-t-2 mt-2">
            <span>Total Ekuitas</span>
            <span>Rp {{ number_format($totalEquity + $currentEarnings, 0, ',', '.') }}</span>
        </div>

        <div class="flex justify-between py-3 mt-4 border-t-4 border-indigo-600 font-bold">
            <span>Total Kewajiban + Ekuitas</span>
            <span>Rp {{ number_format($totalLiabilities + $totalEquity + $currentEarnings, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

@php $diff = $totalAssets - ($totalLiabilities + $totalEquity + $currentEarnings); @endphp
@if(abs($diff) > 0.01)
    <div class="mt-3 px-4 py-2 bg-red-100 text-red-800 rounded text-sm">
         Neraca tidak balance. Selisih: Rp {{ number_format(abs($diff), 0, ',', '.') }}
    </div>
@else
    <div class="mt-3 px-4 py-2 bg-green-100 text-green-800 rounded text-sm">
        ✓ Neraca Balance
    </div>
@endif

@endsection