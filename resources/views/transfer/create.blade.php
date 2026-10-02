@extends('layouts.app')
@section('title', 'Transfer Baru')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Transfer Baru</h1>
        <p class="ln-page-sub">Jurnal akan otomatis dibuat</p>
    </div>
    <a href="{{ route('transfers.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('transfers.store') }}" class="ln-form-card space-y-4 max-w-2xl">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Tanggal *</label>
            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="ln-input">
        </div>
        <div>
            <label class="ln-label">No. Bukti</label>
            <input type="text" name="reference" value="{{ old('reference') }}" class="ln-input" placeholder="Opsional">
        </div>
    </div>

    <div>
        <label class="ln-label">Dari Akun *</label>
        <select name="from_account_id" required class="ln-select">
            <option value="">- Pilih -</option>
            @foreach($accounts as $a)
                <option value="{{ $a->id }}" @selected(old('from_account_id') == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="ln-label">Ke Akun *</label>
        <select name="to_account_id" required class="ln-select">
            <option value="">- Pilih -</option>
            @foreach($accounts as $a)
                <option value="{{ $a->id }}" @selected(old('to_account_id') == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Jumlah (Rp) *</label>
            <input type="number" name="amount" value="{{ old('amount') }}" required min="0.01" step="0.01" class="ln-input">
        </div>
        <div>
            <label class="ln-label">Biaya Admin (Rp)</label>
            <input type="number" name="admin_fee" value="{{ old('admin_fee', 0) }}" min="0" step="0.01" class="ln-input">
        </div>
    </div>

    <div>
        <label class="ln-label">Keterangan</label>
        <textarea name="description" rows="2" class="ln-textarea" placeholder="Misal: Transfer ke rekening BCA">{{ old('description') }}</textarea>
    </div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Simpan Transfer</button>
        <a href="{{ route('transfers.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

@endsection