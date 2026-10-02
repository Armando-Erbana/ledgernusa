@extends('layouts.app')
@section('title', 'Detail Transaksi Kas')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Detail Transaksi</h1>
        <p class="ln-page-sub">{{ $cash->type === 'in' ? 'Kas Masuk' : 'Kas Keluar' }}</p>
    </div>
    <a href="{{ route('cash.index') }}" class="ln-action">← Kembali</a>
</div>

<div class="ln-form-card mb-4">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
        <div>
            <div class="ln-label">Tanggal</div>
            <div class="font-medium">{{ $cash->date->format('d M Y') }}</div>
        </div>
        <div>
            <div class="ln-label">Jenis</div>
            <span class="ln-badge {{ $cash->type === 'in' ? 'posted' : 'draft' }}">
                {{ $cash->type === 'in' ? 'Kas Masuk' : 'Kas Keluar' }}
            </span>
        </div>
        <div>
            <div class="ln-label">Jumlah</div>
            <div class="font-semibold" style="font-size:16px;">Rp {{ number_format($cash->amount, 0, ',', '.') }}</div>
        </div>
        <div>
            <div class="ln-label">Akun Kas/Bank</div>
            <div class="font-medium">{{ $cash->cashAccount->code }} - {{ $cash->cashAccount->name }}</div>
        </div>
        <div>
            <div class="ln-label">Akun Lawan</div>
            <div class="font-medium">{{ $cash->counterAccount->code }} - {{ $cash->counterAccount->name }}</div>
        </div>
        <div>
            <div class="ln-label">Referensi</div>
            <div class="font-medium">{{ $cash->reference ?: '-' }}</div>
        </div>
        <div class="col-span-2 md:col-span-3">
            <div class="ln-label">Keterangan</div>
            <div class="font-medium">{{ $cash->description ?: '-' }}</div>
        </div>
    </div>
</div>

@if($cash->journal)
    <h2 class="ln-section-title">Jurnal Otomatis</h2>
    <div class="ln-table-card mb-4">
        <table class="ln-table">
            <thead>
                <tr>
                    <th>Akun</th>
                    <th class="num">Debit</th>
                    <th class="num">Kredit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cash->journal->entries as $e)
                    <tr>
                        <td>{{ $e->account->code }} - {{ $e->account->name }}</td>
                        <td class="num">{{ $e->debit > 0 ? number_format($e->debit, 0, ',', '.') : '-' }}</td>
                        <td class="num">{{ $e->credit > 0 ? number_format($e->credit, 0, ',', '.') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#FBFBFA">
                    <td style="text-align:right; font-weight:600">Total</td>
                    <td class="num" style="font-weight:600">{{ number_format($cash->journal->totalDebit(), 0, ',', '.') }}</td>
                    <td class="num" style="font-weight:600">{{ number_format($cash->journal->totalCredit(), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
@endif

<div class="flex gap-2">
    <a href="{{ route('cash.edit', $cash) }}" class="ln-btn-outline">Edit</a>
    <form method="POST" action="{{ route('cash.destroy', $cash) }}" onsubmit="return confirm('Hapus transaksi ini?')">
        @csrf @method('DELETE')
        <button class="ln-btn-cancel" style="color:#C0392B; border-color:#C0392B;">Hapus</button>
    </form>
</div>

@endsection