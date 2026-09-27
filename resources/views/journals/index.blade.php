@extends('layouts.app')
@section('title', 'Jurnal Umum')
@section('content')

<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold">Jurnal Umum</h1>
    <a href="{{ route('journals.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded text-sm">+ Jurnal</a>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-3 py-2 text-left">Tanggal</th>
                <th class="px-3 py-2 text-left">Ref</th>
                <th class="px-3 py-2 text-left">Deskripsi</th>
                <th class="px-3 py-2 text-right">Total</th>
                <th class="px-3 py-2 text-center">Status</th>
                <th class="px-3 py-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($journals as $j)
                <tr class="border-t">
                    <td class="px-3 py-2 whitespace-nowrap">{{ $j->date->format('d/m/Y') }}</td>
                    <td class="px-3 py-2">{{ $j->reference }}</td>
                    <td class="px-3 py-2 text-gray-600">{{ \Illuminate\Support\Str::limit($j->description, 40) }}</td>
                    <td class="px-3 py-2 text-right whitespace-nowrap">Rp {{ number_format($j->totalDebit(), 0, ',', '.') }}</td>
                    <td class="px-3 py-2 text-center">
                        <span class="text-xs px-2 py-1 rounded {{ $j->status === 'posted' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $j->status }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-center whitespace-nowrap">
                        <a href="{{ route('journals.show', $j) }}" class="text-indigo-600 text-xs">Lihat</a>
                        @if($j->status !== 'posted')
                            <a href="{{ route('journals.edit', $j) }}" class="text-indigo-600 text-xs ml-2">Edit</a>
                            <form method="POST" action="{{ route('journals.post', $j) }}" class="inline">
                                @csrf
                                <button class="text-green-700 text-xs ml-2">Posting</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">Belum ada jurnal</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $journals->links() }}</div>

@endsection