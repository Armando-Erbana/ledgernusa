@extends('layouts.app')
@section('title', 'COA')
@section('content')

<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold">Chart of Accounts</h1>
    <a href="{{ route('accounts.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded text-sm">+ Akun</a>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-3 py-2 text-left">Kode</th>
                <th class="px-3 py-2 text-left">Nama</th>
                <th class="px-3 py-2 text-left">Tipe</th>
                <th class="px-3 py-2 text-right">Saldo</th>
                <th class="px-3 py-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $a)
                <tr class="border-t">
                    <td class="px-3 py-2 font-mono">{{ $a->code }}</td>
                    <td class="px-3 py-2">{{ $a->name }}</td>
                    <td class="px-3 py-2 capitalize text-xs">{{ $a->type }}</td>
                    <td class="px-3 py-2 text-right">Rp {{ number_format($a->balance, 0, ',', '.') }}</td>
                    <td class="px-3 py-2 text-center whitespace-nowrap">
                        <a href="{{ route('accounts.edit', $a) }}" class="text-indigo-600 text-xs">Edit</a>
                        <form method="POST" action="{{ route('accounts.destroy', $a) }}" class="inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 text-xs ml-2">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-3 py-6 text-center text-gray-400">Belum ada akun</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection