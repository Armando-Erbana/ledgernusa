@extends('layouts.app')
@section('title', 'Kas & Bank')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Kas & Bank</h1>
        <p class="ln-page-sub">Catat semua transaksi kas masuk & keluar</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('cash.create', ['type' => 'out']) }}" class="ln-btn-outline">− Kas Keluar</a>
        <a href="{{ route('cash.create', ['type' => 'in']) }}" class="ln-btn-primary">+ Kas Masuk</a>
    </div>
</div>

{{-- Ringkasan --}}
<div class="grid grid-cols-2 gap-3 mb-6">
    <div class="ln-stat kas">
        <div class="top">
            <span class="lbl">Total Kas Masuk</span>
            <span class="ico">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
            </span>
        </div>
        <div class="val">Rp {{ number_format($totalIn, 0, ',', '.') }}</div>
    </div>
    <div class="ln-stat jurnal">
        <div class="top">
            <span class="lbl">Total Kas Keluar</span>
            <span class="ico">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg>
            </span>
        </div>
        <div class="val">Rp {{ number_format($totalOut, 0, ',', '.') }}</div>
    </div>
</div>

{{-- Filter --}}
<form method="GET" class="ln-form-card mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
    <div>
        <label class="ln-label">Jenis</label>
        <select name="type" class="ln-select">
            <option value="">Semua</option>
            <option value="in" @selected(request('type') === 'in')>Kas Masuk</option>
            <option value="out" @selected(request('type') === 'out')>Kas Keluar</option>
        </select>
    </div>
    <div>
        <label class="ln-label">Dari</label>
        <input type="date" name="from" value="{{ request('from') }}" class="ln-input">
    </div>
    <div>
        <label class="ln-label">Sampai</label>
        <input type="date" name="to" value="{{ request('to') }}" class="ln-input">
    </div>
    <div class="flex items-end gap-2">
        <button class="ln-btn-primary w-full justify-center">Filter</button>
        @if(request()->hasAny(['type','from','to']))
            <a href="{{ route('cash.index') }}" class="ln-btn-outline">Reset</a>
        @endif
    </div>
</form>

{{-- Tabel --}}
<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Akun Kas</th>
                <th>Akun Lawan</th>
                <th>Keterangan</th>
                <th class="num">Jumlah</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($transactions as $t)
            <tr>
                <td class="whitespace-nowrap">{{ $t->date->format('d/m/Y') }}</td>
                <td>
                    <span class="ln-badge {{ $t->type === 'in' ? 'posted' : 'draft' }}">
                        {{ $t->type === 'in' ? 'Masuk' : 'Keluar' }}
                    </span>
                </td>
                <td>{{ $t->cashAccount->code }} - {{ $t->cashAccount->name }}</td>
                <td>{{ $t->counterAccount->code }} - {{ $t->counterAccount->name }}</td>
                <td class="text-gray-600">{{ \Illuminate\Support\Str::limit($t->description, 40) }}</td>
                <td class="num">Rp {{ number_format($t->amount, 0, ',', '.') }}</td>
                <td class="center whitespace-nowrap">
                    <a href="{{ route('cash.show', $t) }}" class="ln-action">Lihat</a>
                    <span class="ln-action-sep">|</span>
                    <a href="{{ route('cash.edit', $t) }}" class="ln-action">Edit</a>
                    <span class="ln-action-sep">|</span>
                    <form method="POST" action="{{ route('cash.destroy', $t) }}" class="inline" onsubmit="return confirm('Hapus transaksi ini? Jurnal terkait juga akan dihapus.')">
                        @csrf @method('DELETE')
                        <button class="ln-action red">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty">Belum ada transaksi kas</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="ln-pagination">{{ $transactions->links() }}</div>

@endsection