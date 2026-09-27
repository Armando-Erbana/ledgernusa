@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<h1 class="text-xl font-bold mb-4">Dashboard</h1>

<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white p-4 rounded shadow">
        <div class="text-xs text-gray-500">Saldo Kas</div>
        <div class="text-lg font-bold text-green-700">Rp {{ number_format($totalKas, 0, ',', '.') }}</div>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <div class="text-xs text-gray-500">Saldo Bank</div>
        <div class="text-lg font-bold text-blue-700">Rp {{ number_format($totalBank, 0, ',', '.') }}</div>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <div class="text-xs text-gray-500">Total Akun</div>
        <div class="text-lg font-bold">{{ \App\Models\Account::where('company_id', session('company_id'))->count() }}</div>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <div class="text-xs text-gray-500">Total Jurnal</div>
        <div class="text-lg font-bold">{{ \App\Models\Journal::where('company_id', session('company_id'))->count() }}</div>
    </div>
</div>

<div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('journals.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">+ Jurnal Baru</a>
    <a href="{{ route('accounts.create') }}" class="px-4 py-2 bg-white border rounded hover:bg-gray-50">+ Akun COA</a>
    <a href="{{ route('contacts.create') }}" class="px-4 py-2 bg-white border rounded hover:bg-gray-50">+ Kontak</a>
</div>

<h2 class="text-lg font-semibold mb-2">Jurnal Terbaru</h2>
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-3 py-2 text-left">Tanggal</th>
                <th class="px-3 py-2 text-left">Referensi</th>
                <th class="px-3 py-2 text-right">Total</th>
                <th class="px-3 py-2 text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentJournals as $j)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $j->date->format('d/m/Y') }}</td>
                    <td class="px-3 py-2">
                        <a href="{{ route('journals.show', $j) }}" class="text-indigo-600 hover:underline">
                            {{ $j->reference ?: '#' . $j->id }}
                        </a>
                    </td>
                    <td class="px-3 py-2 text-right">Rp {{ number_format($j->totalDebit(), 0, ',', '.') }}</td>
                    <td class="px-3 py-2 text-center">
                        <span class="px-2 py-1 text-xs rounded {{ $j->status === 'posted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $j->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-3 py-6 text-center text-gray-400">Belum ada jurnal</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection