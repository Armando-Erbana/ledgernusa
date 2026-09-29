@extends('layouts.app')
@section('title', 'Laporan')
@section('content')

<h1 class="text-xl font-bold mb-4">Laporan Keuangan</h1>

<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    <a href="{{ route('reports.ledger') }}" class="bg-white p-4 rounded shadow hover:shadow-md transition">
        <div class="text-2xl mb-1"></div>
        <div class="font-semibold">Buku Besar</div>
        <div class="text-xs text-gray-500">Mutasi & saldo per akun</div>
    </a>
    <a href="{{ route('reports.trial-balance') }}" class="bg-white p-4 rounded shadow hover:shadow-md transition">
        <div class="text-2xl mb-1"></div>
        <div class="font-semibold">Neraca Saldo</div>
        <div class="text-xs text-gray-500">Trial balance per periode</div>
    </a>
    <a href="{{ route('reports.income-statement') }}" class="bg-white p-4 rounded shadow hover:shadow-md transition">
        <div class="text-2xl mb-1"></div>
        <div class="font-semibold">Laba Rugi</div>
        <div class="text-xs text-gray-500">Pendapatan & beban</div>
    </a>
    <a href="{{ route('reports.balance-sheet') }}" class="bg-white p-4 rounded shadow hover:shadow-md transition">
        <div class="text-2xl mb-1"></div>
        <div class="font-semibold">Neraca</div>
        <div class="text-xs text-gray-500">Aset, kewajiban, ekuitas</div>
    </a>
</div>

@endsection