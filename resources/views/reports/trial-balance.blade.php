@extends('layouts.app')
@section('title', 'Neraca Saldo')
@section('content')

<h1 class="text-xl font-bold mb-4">Neraca Saldo</h1>
<a href="{{ route('export.trial-balance-pdf', ['from' => $from, 'to' => $to]) }}" target="_blank" class="ln-btn-outline">
    📄 Export PDF
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

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-3 py-2 text-left">Kode</th>
                <th class="px-3 py-2 text-left">Nama Akun</th>
                <th class="px-3 py-2 text-right">Debit</th>
                <th class="px-3 py-2 text-right">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                <tr class="border-t">
                    <td class="px-3 py-2 font-mono text-xs">{{ $r['account']->code }}</td>
                    <td class="px-3 py-2">{{ $r['account']->name }}</td>
                    <td class="px-3 py-2 text-right">{{ $r['debit'] > 0 ? number_format($r['debit'], 0, ',', '.') : '-' }}</td>
                    <td class="px-3 py-2 text-right">{{ $r['credit'] > 0 ? number_format($r['credit'], 0, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-3 py-6 text-center text-gray-400">Tidak ada data</td></tr>
            @endforelse
        </tbody>
        <tfoot class="bg-gray-50 font-semibold">
            <tr>
                <td colspan="2" class="px-3 py-2 text-right">Total</td>
                <td class="px-3 py-2 text-right">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                <td class="px-3 py-2 text-right">{{ number_format($totalCredit, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>

@if(abs($totalDebit - $totalCredit) > 0.01)
    <div class="mt-3 px-4 py-2 bg-red-100 text-red-800 rounded text-sm">
         Total debit dan kredit tidak balance. Periksa jurnal Anda.
    </div>
@else
    <div class="mt-3 px-4 py-2 bg-green-100 text-green-800 rounded text-sm">
        ✓ Balance
    </div>
@endif

@endsection