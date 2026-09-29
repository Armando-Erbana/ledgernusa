@extends('layouts.app')
@section('title', 'Detail Jurnal')
@section('content')

<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold">Jurnal #{{ $journal->id }}</h1>
    <a href="{{ route('journals.index') }}" class="text-sm text-indigo-600">← Kembali</a>
</div>

<div class="bg-white p-4 rounded shadow space-y-2 mb-4">
    <div class="grid grid-cols-2 gap-3 text-sm">
        <div><span class="text-gray-500">Tanggal:</span> {{ $journal->date->format('d/m/Y') }}</div>
        <div><span class="text-gray-500">Referensi:</span> {{ $journal->reference ?: '-' }}</div>
        <div class="col-span-2"><span class="text-gray-500">Deskripsi:</span> {{ $journal->description ?: '-' }}</div>
        <div>
            <span class="text-gray-500">Status:</span>
            <span class="text-xs px-2 py-1 rounded {{ $journal->status === 'posted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $journal->status }}</span>
        </div>
    </div>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-3 py-2 text-left">Akun</th>
                <th class="px-3 py-2 text-right">Debit</th>
                <th class="px-3 py-2 text-right">Kredit</th>
            </tr>
        </thead>
        <tbody>
        @foreach($journal->entries as $e)
            <tr class="border-t">
                <td class="px-3 py-2">{{ $e->account->code }} - {{ $e->account->name }}</td>
                <td class="px-3 py-2 text-right">{{ $e->debit > 0 ? number_format($e->debit, 2, ',', '.') : '-' }}</td>
                <td class="px-3 py-2 text-right">{{ $e->credit > 0 ? number_format($e->credit, 2, ',', '.') : '-' }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot class="bg-gray-50 font-semibold">
            <tr>
                <td class="px-3 py-2 text-right">Total</td>
                <td class="px-3 py-2 text-right">{{ number_format($journal->totalDebit(), 2, ',', '.') }}</td>
                <td class="px-3 py-2 text-right">{{ number_format($journal->totalCredit(), 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>

@if($journal->status !== 'posted')
    <form method="POST" action="{{ route('journals.post', $journal) }}" class="mt-4">
        @csrf
        <button class="px-4 py-2 bg-green-600 text-white rounded">Posting Jurnal</button>
    </form>
@endif

@endsection