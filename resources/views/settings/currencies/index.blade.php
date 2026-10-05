@extends('layouts.app')

@section('title', 'Currencies')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Currencies</h1>
    <a href="{{ route('settings.currencies.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded">+ Tambah</a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-800 p-3 rounded mb-3">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-red-100 text-red-800 p-3 rounded mb-3">{{ session('error') }}</div>
@endif

<table class="table-auto w-full border bg-white">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 border text-left">Code</th>
            <th class="p-2 border text-left">Name</th>
            <th class="p-2 border text-left">Symbol</th>
            <th class="p-2 border text-center">Decimals</th>
            <th class="p-2 border text-center">Status</th>
            <th class="p-2 border text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($currencies as $c)
            <tr class="border-t">
                <td class="p-2 border font-mono">{{ $c->code }}</td>
                <td class="p-2 border">{{ $c->name }}</td>
                <td class="p-2 border">{{ $c->symbol }}</td>
                <td class="p-2 border text-center">{{ $c->decimal_places }}</td>
                <td class="p-2 border text-center">{{ $c->is_active ? '✓' : '✗' }}</td>
                <td class="p-2 border text-center">
                    <a href="{{ route('settings.currencies.edit', $c) }}" class="text-blue-600">Edit</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="p-4 text-center text-gray-400">Belum ada data</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">{{ $currencies->links() }}</div>
@endsection