@extends('layouts.app')
@section('title', 'Detail Transfer')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Detail Transfer</h1>
    </div>
    <a href="{{ route('transfers.index') }}" class="ln-action">← Kembali</a>
</div>

<div class="ln-form-card mb-4">
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
        <div><div class="ln-label">Tanggal</div><div class="font-medium">{{ $transfer->date->format('d M Y') }}</div></div>
        <div><div class="ln-label">Referensi</div><div class="font-medium">{{ $transfer->reference ?: '-' }}</div></div>
        <div><div class="ln-label">Jumlah</div><div class="font-semibold" style="font-size:16px;">Rp {{ number_format($transfer->amount, 0, ',', '.') }}</div></div>
        <div><div class="ln-label">Dari</div><div class="font-medium">{{ $transfer->fromAccount->code }} - {{ $transfer->fromAccount->name }}</div></div>
        <div><div class="ln-label">Ke</div><div class="font-medium">{{ $transfer->toAccount->code }} - {{ $transfer->toAccount->name }}</div></div>
        <div><div class="ln-label">Biaya Admin</div><div class="font-medium">Rp {{ number_format($transfer->admin_fee, 0, ',', '.') }}</div></div>
        <div class="col-span-2 md:col-span-3"><div class="ln-label">Keterangan</div><div>{{ $transfer->description ?: '-' }}</div></div>
    </div>
</div>

@if($transfer->journal)
    <h2 class="ln-section-title">Jurnal Otomatis</h2>
    <div class="ln-table-card mb-4">
        <table class="ln-table">
            <thead><tr><th>Akun</th><th class="num">Debit</th><th class="num">Kredit</th></tr></thead>
            <tbody>
            @foreach($transfer->journal->entries as $e)
                <tr>
                    <td>{{ $e->account->code }} - {{ $e->account->name }}</td>
                    <td class="num">{{ $e->debit > 0 ? number_format($e->debit, 0, ',', '.') : '-' }}</td>
                    <td class="num">{{ $e->credit > 0 ? number_format($e->credit, 0, ',', '.') : '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

<div class="flex gap-2">
    <a href="{{ route('transfers.edit', $transfer) }}" class="ln-btn-outline">Edit</a>
    <form method="POST" action="{{ route('transfers.destroy', $transfer) }}" onsubmit="return confirm('Hapus transfer?')">
        @csrf @method('DELETE')
        <button class="ln-btn-cancel" style="color:#C0392B; border-color:#C0392B;">Hapus</button>
    </form>
</div>

@endsection