@extends('layouts.app')
@section('title', 'Jurnal Umum')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Jurnal Umum</h1>
        <p class="ln-page-sub">Semua transaksi jurnal yang tercatat</p>
    </div>
    <a href="{{ route('journals.create') }}" class="ln-btn-primary">+ Jurnal</a>
</div>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Ref</th>
                <th>Deskripsi</th>
                <th class="num">Total</th>
                <th class="center">Status</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($journals as $j)
            <tr>
                <td class="whitespace-nowrap">{{ $j->date->format('d/m/Y') }}</td>
                <td>{{ $j->reference }}</td>
                <td style="color: var(--ln-ink-soft);">{{ \Illuminate\Support\Str::limit($j->description, 40) }}</td>
                <td class="num whitespace-nowrap">Rp {{ number_format($j->totalDebit(), 0, ',', '.') }}</td>
                <td class="center">
                    <span class="ln-badge {{ $j->status === 'posted' ? 'posted' : 'draft' }}">{{ $j->status }}</span>
                </td>
                <td class="center whitespace-nowrap">
                    <a href="{{ route('journals.show', $j) }}" class="ln-action">Lihat</a>
                    @if($j->status !== 'posted')
                        <span class="ln-action-sep">·</span>
                        <a href="{{ route('journals.edit', $j) }}" class="ln-action">Edit</a>
                        <span class="ln-action-sep">·</span>
                        <form method="POST" action="{{ route('journals.post', $j) }}" class="inline">
                            @csrf
                            <button class="ln-action green">Posting</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">Belum ada jurnal</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="ln-pagination">{{ $journals->links() }}</div>

@endsection