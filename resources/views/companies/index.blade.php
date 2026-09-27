@extends('layouts.app')
@section('title', 'Company')
@section('content')

<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold">Daftar Company</h1>
    <a href="{{ route('companies.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded text-sm">+ Baru</a>
</div>

<div class="grid gap-3">
    @foreach($companies as $c)
        <div class="bg-white p-4 rounded shadow flex justify-between items-center">
            <div>
                <div class="font-semibold">{{ $c->name }}</div>
                <div class="text-xs text-gray-500">{{ $c->currency }} · Role: {{ $c->pivot->role }}</div>
            </div>
            @if(session('company_id') == $c->id)
                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Aktif</span>
            @else
                <a href="{{ route('companies.switch', $c->id) }}" class="text-sm text-indigo-600 hover:underline">Pilih</a>
            @endif
        </div>
    @endforeach
</div>

@endsection