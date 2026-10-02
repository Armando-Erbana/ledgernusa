@extends('layouts.app')
@section('title', 'Laporan PPh 23')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Laporan PPh 23</h1>
        <p class="ln-page-sub">Pajak atas jasa</p>
    </div>
    <a href="{{ route('tax.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="GET" class="ln-form-card mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
    <div><label class="ln-label">Dari</label><input type="date" name="from" value="{{ $from }}" class="ln-input"></div>
    <div><label class="ln-label">Sampai</label><input type="date" name="to" value="{{ $to }}" class="ln-input"></div>
    <div class="flex items-end"><button class="ln-btn-primary w-full justify-center">Tampilkan</button></div>
</form>

<div class="ln-stat bank mb-4">
    <div class="lbl">Total PPh 23 Periode Ini</div>
    <div class="val">Rp {{ number_format($totalPph, 0, ',', '.') }}</div>
</div>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Referensi</th>
                <th>Deskripsi</th>
                <th class="num">PPh 23</th>
            </tr>
        </thead>
        <tbody>
        @forelse($pphList as $e)
            <tr>
                <td>{{ $e->journal->date->format('d/m/Y') }}</td>
                <td><a href="{{ route('journals.show', $e->journal) }}" class="ref">{{ $e->journal->reference ?: '#' . $e->journal->id }}</a></td>
                <td>{{ $e->journal->description }}</td>
                <td class="num">Rp {{ number_format($e->credit, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty">Tidak ada transaksi PPh 23 periode ini</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection