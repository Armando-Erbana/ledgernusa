@extends('layouts.app')
@section('title', 'Kontak')
@section('content')

<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold">Kontak</h1>
    <a href="{{ route('contacts.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded text-sm">+ Kontak</a>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-3 py-2 text-left">Nama</th>
                <th class="px-3 py-2 text-left">Tipe</th>
                <th class="px-3 py-2 text-left">Telepon</th>
                <th class="px-3 py-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($contacts as $c)
            <tr class="border-t">
                <td class="px-3 py-2">{{ $c->name }}</td>
                <td class="px-3 py-2 capitalize text-xs">{{ $c->type }}</td>
                <td class="px-3 py-2">{{ $c->phone }}</td>
                <td class="px-3 py-2 text-center">
                    <a href="{{ route('contacts.edit', $c) }}" class="text-indigo-600 text-xs">Edit</a>
                    <form method="POST" action="{{ route('contacts.destroy', $c) }}" class="inline" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 text-xs ml-2">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-3 py-6 text-center text-gray-400">Belum ada kontak</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection