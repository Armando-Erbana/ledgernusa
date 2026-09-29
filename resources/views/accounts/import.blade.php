@extends('layouts.app')
@section('title', 'Import COA')
@section('content')

<h1 class="text-xl font-bold mb-4">Import COA dari CSV</h1>

<div class="bg-white p-4 rounded shadow space-y-3">
    <div class="text-sm text-gray-600 bg-blue-50 p-3 rounded">
        <div class="font-semibold mb-1">Format CSV:</div>
        <div class="font-mono text-xs">code,name,type</div>
        <div class="font-mono text-xs">101,Kas,asset</div>
        <div class="font-mono text-xs">102,Bank,asset</div>
        <div class="font-mono text-xs">401,Pendapatan,revenue</div>
        <div class="mt-2">Tipe valid: <span class="font-mono">asset, liability, equity, revenue, expense</span></div>
    </div>

    <form method="POST" action="{{ route('accounts.import.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" accept=".csv,.txt" required class="w-full border rounded px-3 py-2">
        <div class="pt-3">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Import</button>
            <a href="{{ route('accounts.index') }}" class="px-4 py-2 bg-gray-100 rounded">Batal</a>
        </div>
    </form>
</div>

@endsection