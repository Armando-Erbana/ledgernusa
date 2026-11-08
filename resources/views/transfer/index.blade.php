@extends('layouts.app')
@section('title', 'Transfer Antar Akun')
@section('content')

<style>
/* ===== Hanya tombol: gelap → biru (2-tone: #2563EB, #1E3A8A) ===== */
.ln-btn-primary{
    background:linear-gradient(180deg,#2563EB,#1E3A8A) !important;
    border-color:transparent !important;
    color:#fff !important;
}
.ln-btn-primary:hover{
    background:linear-gradient(180deg,#1E3A8A,#1E3A8A) !important;
}
.ln-btn-outline{
    color:#1E3A8A !important;
    border-color:rgba(37,99,235,.35) !important;
}
.ln-btn-outline:hover{
    background:rgba(37,99,235,.08) !important;
    border-color:rgba(37,99,235,.55) !important;
}
</style>

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Transfer</h1>
        <p class="ln-page-sub">Pindah dana antar kas/bank</p>
    </div>
    <a href="{{ route('transfers.create') }}" class="ln-btn-primary">+ Transfer</a>
</div>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Dari</th>
                <th>Ke</th>
                <th>Keterangan</th>
                <th class="num">Jumlah</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($transfers as $t)
            <tr>
                <td class="whitespace-nowrap">{{ $t->date->format('d/m/Y') }}</td>
                <td>{{ $t->fromAccount->name }}</td>
                <td>{{ $t->toAccount->name }}</td>
                <td class="text-gray-600">{{ \Illuminate\Support\Str::limit($t->description, 30) }}</td>
                <td class="num">Rp {{ number_format($t->amount, 0, ',', '.') }}</td>
                <td class="center whitespace-nowrap">
                    <a href="{{ route('transfers.show', $t) }}" class="ln-action">Lihat</a>
                    <span class="ln-action-sep">|</span>
                    <a href="{{ route('transfers.edit', $t) }}" class="ln-action">Edit</a>
                    <span class="ln-action-sep">|</span>
                    <form method="POST" action="{{ route('transfers.destroy', $t) }}" class="inline" onsubmit="return confirm('Hapus transfer?')">
                        @csrf @method('DELETE')
                        <button class="ln-action red">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">Belum ada transfer</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="ln-pagination">{{ $transfers->links() }}</div>

@endsection