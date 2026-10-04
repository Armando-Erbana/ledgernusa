@extends('layouts.app')
@section('title', 'Rekonsiliasi Bank')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Rekonsiliasi Bank</h1>
        <p class="ln-page-sub">Cocokkan mutasi bank dengan catatan sistem</p>
    </div>
    <a href="{{ route('bank-rec.create') }}" class="ln-btn-primary">+ Import Mutasi</a>
</div>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Akun Bank</th>
                <th>Periode</th>
                <th class="num">Saldo Awal</th>
                <th class="num">Saldo Akhir</th>
                <th class="center">Status</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($statements as $s)
            <tr>
                <td>{{ $s->account->code }} - {{ $s->account->name }}</td>
                <td class="whitespace-nowrap">{{ $s->period_start->format('d/m/Y') }} - {{ $s->period_end->format('d/m/Y') }}</td>
                <td class="num">Rp {{ number_format($s->opening_balance, 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format($s->closing_balance, 0, ',', '.') }}</td>
                <td class="center">
                    <span class="ln-badge {{ $s->status === 'reconciled' ? 'posted' : 'draft' }}">{{ $s->status }}</span>
                </td>
                <td class="center whitespace-nowrap">
                    <a href="{{ route('bank-rec.show', $s) }}" class="ln-action">Lihat</a>
                    <span class="ln-action-sep">|</span>
                    <form method="POST" action="{{ route('bank-rec.destroy', $s) }}" class="inline" onsubmit="return confirm('Hapus rekonsiliasi ini?')">
                        @csrf @method('DELETE')
                        <button class="ln-action red">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">Belum ada rekonsiliasi</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="ln-pagination">{{ $statements->links() }}</div>

@endsection