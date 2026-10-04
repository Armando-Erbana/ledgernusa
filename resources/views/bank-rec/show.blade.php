@extends('layouts.app')
@section('title', 'Detail Rekonsiliasi')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">{{ $bankRec->account->name }}</h1>
        <p class="ln-page-sub">
            {{ $bankRec->period_start->format('d M Y') }} - {{ $bankRec->period_end->format('d M Y') }}
            · Status: <b>{{ $bankRec->status }}</b>
        </p>
    </div>
    <a href="{{ route('bank-rec.index') }}" class="ln-action">← Kembali</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
    <div class="ln-stat bank">
        <div class="lbl">Saldo Akhir Statement</div>
        <div class="val">Rp {{ number_format($statementBalance, 0, ',', '.') }}</div>
    </div>
    <div class="ln-stat kas">
        <div class="lbl">Saldo Sistem (Buku)</div>
        <div class="val">Rp {{ number_format($systemBalance, 0, ',', '.') }}</div>
    </div>
    <div class="ln-stat {{ abs($statementBalance - $systemBalance) < 1 ? 'in' : 'out' }}">
        <div class="lbl">Selisih</div>
        <div class="val">Rp {{ number_format(abs($statementBalance - $systemBalance), 0, ',', '.') }}</div>
    </div>
</div>

<div class="flex flex-wrap gap-2 mb-4">
    <form method="POST" action="{{ route('bank-rec.auto-match', $bankRec) }}">
        @csrf
        <button class="ln-btn-primary">🔍 Auto-Match</button>
    </form>
    @if($bankRec->status === 'draft')
        <form method="POST" action="{{ route('bank-rec.finalize', $bankRec) }}" onsubmit="return confirm('Tandai rekonsiliasi selesai?')">
            @csrf
            <button class="ln-btn-outline">✓ Selesai</button>
        </form>
    @endif
</div>

<h2 class="ln-section-title">Baris Mutasi ({{ $bankRec->lines->count() }} baris)</h2>
<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Ref</th>
                <th class="num">Masuk</th>
                <th class="num">Keluar</th>
                <th class="num">Saldo</th>
                <th class="center">Status</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($bankRec->lines as $line)
            <tr>
                <td class="whitespace-nowrap">{{ $line->date->format('d/m/Y') }}</td>
                <td>{{ $line->description }}</td>
                <td>{{ $line->reference ?: '-' }}</td>
                <td class="num" style="color:#1E7A4C">{{ $line->debit > 0 ? number_format($line->debit, 0, ',', '.') : '-' }}</td>
                <td class="num" style="color:#C0392B">{{ $line->credit > 0 ? number_format($line->credit, 0, ',', '.') : '-' }}</td>
                <td class="num">{{ $line->balance ? number_format($line->balance, 0, ',', '.') : '-' }}</td>
                <td class="center">
                    @if($line->match_status === 'matched')
                        <span class="ln-badge posted">✓ Cocok</span>
                    @elseif($line->match_status === 'excluded')
                        <span class="ln-badge draft">Dikecualikan</span>
                    @else
                        <span class="ln-badge unpaid">Belum</span>
                    @endif
                </td>
                <td class="center whitespace-nowrap">
                    @if($line->match_status === 'unmatched')
                        <form method="POST" action="{{ route('bank-rec.lines.exclude', [$bankRec, $line]) }}" class="inline">
                            @csrf
                            <button class="ln-action">Kecualikan</button>
                        </form>
                    @elseif($line->match_status === 'matched')
                        <form method="POST" action="{{ route('bank-rec.lines.unmatch', [$bankRec, $line]) }}" class="inline">
                            @csrf
                            <button class="ln-action red">Batalkan</button>
                        </form>
                    @else
                        <span class="text-gray-400 text-xs">-</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection