@extends('layouts.app')
@section('title', 'Buku Besar')
@section('content')

<h1 class="text-xl font-bold mb-4">Buku Besar</h1>

<form method="GET" class="bg-white p-4 rounded shadow mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
    <div>
        <label class="block text-xs text-gray-600">Akun</label>
        <select name="account_id" class="w-full border rounded px-3 py-2 text-sm">
            <option value="">- Pilih Akun -</option>
            @foreach($accounts as $a)
                <option value="{{ $a->id }}" @selected($account && $account->id == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
            @endforeach
        </select>
    </div>
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

@if($account)
    <div class="bg-white p-4 rounded shadow mb-3">
        <div class="font-semibold">{{ $account->code }} - {{ $account->name }}</div>
        <div class="text-xs text-gray-500">Normal Balance: {{ $account->normal_balance }}</div>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-3 py-2 text-left">Tanggal</th>
                    <th class="px-3 py-2 text-left">Referensi</th>
                    <th class="px-3 py-2 text-left">Deskripsi</th>
                    <th class="px-3 py-2 text-right">Debit</th>
                    <th class="px-3 py-2 text-right">Kredit</th>
                    <th class="px-3 py-2 text-right">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-t bg-gray-50">
                    <td colspan="5" class="px-3 py-2 text-right font-semibold">Saldo Awal</td>
                    <td class="px-3 py-2 text-right font-semibold">{{ number_format($openingBalance, 0, ',', '.') }}</td>
                </tr>
                @php $running = $openingBalance; @endphp
                @forelse($entries as $e)
                    @php
                        $running += $account->normal_balance === 'debit'
                            ? ($e->debit - $e->credit)
                            : ($e->credit - $e->debit);
                    @endphp
                    <tr class="border-t">
                        <td class="px-3 py-2 whitespace-nowrap">{{ $e->journal->date->format('d/m/Y') }}</td>
                        <td class="px-3 py-2">{{ $e->journal->reference ?: '#' . $e->journal->id }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $e->description ?: $e->journal->description }}</td>
                        <td class="px-3 py-2 text-right">{{ $e->debit > 0 ? number_format($e->debit, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ $e->credit > 0 ? number_format($e->credit, 0, ',', '.') : '-' }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($running, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">Tidak ada mutasi</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 font-semibold">
                <tr>
                    <td colspan="5" class="px-3 py-2 text-right">Saldo Akhir</td>
                    <td class="px-3 py-2 text-right">{{ number_format($closingBalance, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
@else
    <div class="bg-white p-6 rounded shadow text-center text-gray-400">
        Pilih akun untuk menampilkan buku besar.
    </div>
@endif

@endsection