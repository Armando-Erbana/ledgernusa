@extends('layouts.app')
@section('title', 'Edit Transaksi Kas')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Edit Transaksi</h1>
        <p class="ln-page-sub">{{ $cash->type === 'in' ? 'Kas Masuk' : 'Kas Keluar' }}</p>
    </div>
    <a href="{{ route('cash.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('cash.update', $cash) }}" class="ln-form-card space-y-4 max-w-2xl">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Tanggal</label>
            <input type="date" name="date" value="{{ old('date', $cash->date->format('Y-m-d')) }}" required class="ln-input">
        </div>
        <div>
            <label class="ln-label">No. Bukti</label>
            <input type="text" name="reference" value="{{ old('reference', $cash->reference) }}" class="ln-input">
        </div>
    </div>

    <div>
        <label class="ln-label">Akun Kas/Bank</label>
        <select name="cash_account_id" required class="ln-select">
            @foreach($cashAccounts as $a)
                <option value="{{ $a->id }}" @selected(old('cash_account_id', $cash->cash_account_id) == $a->id)>
                    {{ $a->code }} - {{ $a->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="ln-label">Akun Lawan</label>
        <select name="counter_account_id" required class="ln-select">
            @foreach($allAccounts as $a)
                <option value="{{ $a->id }}" @selected(old('counter_account_id', $cash->counter_account_id) == $a->id)>
                    {{ $a->code }} - {{ $a->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="ln-label">Jumlah (Rp)</label>
        <input type="number" name="amount" value="{{ old('amount', $cash->amount) }}" required min="0.01" step="0.01" class="ln-input">
    </div>

    <div>
        <label class="ln-label">Kontak</label>
        <select name="contact_id" class="ln-select">
            <option value="">- Tidak ada -</option>
            @foreach($contacts as $c)
                <option value="{{ $c->id }}" @selected(old('contact_id', $cash->contact_id) == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="ln-label">Keterangan</label>
        <textarea name="description" rows="2" class="ln-textarea">{{ old('description', $cash->description) }}</textarea>
    </div>

    <div class="ln-alert ln-alert-error">
        ⚠️ Mengubah transaksi akan <strong>menghapus jurnal lama</strong> dan membuat jurnal baru.
    </div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Update & Regenerate Jurnal</button>
        <a href="{{ route('cash.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

@endsection