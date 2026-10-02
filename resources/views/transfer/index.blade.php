@extends('layouts.app')
@section('title', 'Transfer Antar Akun')
@section('content')

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