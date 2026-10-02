@extends('layouts.app')
@section('title', 'Laba Rugi')
@section('content')

<h1 class="text-xl font-bold mb-4">Laporan Laba Rugi</h1>
<a href="{{ route('export.income-statement-pdf', ['from' => $from, 'to' => $to]) }}" target="_blank" class="ln-btn-outline">
     Export PDF
</a>

<form method="GET" class="bg-white p-4 rounded shadow mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
    <div>
        <label class="block text-xs text-gray-600">Dari</label>
        <input type="date" name="from" value="{{ $from }}" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-xs text-gray-600">Sampai</label>
        <input type="date" name="to" value="{{ $to }}" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div class="flex items-end">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded text-sm w-full">Tampilkan</button>
    </div>
</form>

<div class="bg-white rounded shadow p-4">
    <h2 class="font-semibold text-gray-700 mb-2">Pendapatan</h2>
    @forelse($revenues as $r)
        <div class="flex justify-between py-1 text-sm border-b">
            <span>{{ $r->code }} - {{ $r->name }}</span>
            <span>Rp {{ number_format($r->period_balance, 0, ',', '.') }}</span>
        </div>
    @empty
        <div class="text-gray-400 text-sm">Belum ada pendapatan</div>
    @endforelse
    <div class="flex justify-between py-2 font-semibold border-t-2 mt-2">
        <span>Total Pendapatan</span>
        <span>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
    </div>

    <h2 class="font-semibold text-gray-700 mb-2 mt-6">Beban</h2>
    @forelse($expenses as $e)
        <div class="flex justify-between py-1 text-sm border-b">
            <span>{{ $e->code }} - {{ $e->name }}</span>
            <span>Rp {{ number_format($e->period_balance, 0, ',', '.') }}</span>
        </div>
    @empty
        <div class="text-gray-400 text-sm">Belum ada beban</div>
    @endforelse
    <div class="flex justify-between py-2 font-semibold border-t-2 mt-2">
        <span>Total Beban</span>
        <span>Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
    </div>

    <div class="flex justify-between py-3 mt-4 border-t-4 border-indigo-600 text-lg font-bold {{ $netIncome >= 0 ? 'text-green-700' : 'text-red-700' }}">
        <span>{{ $netIncome >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}</span>
        <span>Rp {{ number_format(abs($netIncome), 0, ',', '.') }}</span>
    </div>
</div>

@endsection