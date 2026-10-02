@extends('layouts.app')
@section('title', 'Edit Transfer')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Edit Transfer</h1>
    </div>
    <a href="{{ route('transfers.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('transfers.update', $transfer) }}" class="ln-form-card space-y-4 max-w-2xl">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Tanggal *</label>
            <input type="date" name="date" value="{{ old('date', $transfer->date->format('Y-m-d')) }}" required class="ln-input">
        </div>
        <div>
            <label class="ln-label">No. Bukti</label>
            <input type="text" name="reference" value="{{ old('reference', $transfer->reference) }}" class="ln-input">
        </div>
    </div>

    <div>
        <label class="ln-label">Dari Akun *</label>
        <select name="from_account_id" required class="ln-select">
            @foreach($accounts as $a)
                <option value="{{ $a->id }}" @selected(old('from_account_id', $transfer->from_account_id) == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="ln-label">Ke Akun *</label>
        <select name="to_account_id" required class="ln-select">
            @foreach($accounts as $a)
                <option value="{{ $a->id }}" @selected(old('to_account_id', $transfer->to_account_id) == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Jumlah (Rp) *</label>
            <input type="number" name="amount" value="{{ old('amount', $transfer->amount) }}" required min="0.01" step="0.01" class="ln-input">
        </div>
        <div>
            <label class="ln-label">Biaya Admin (Rp)</label>
            <input type="number" name="admin_fee" value="{{ old('admin_fee', $transfer->admin_fee) }}" min="0" step="0.01" class="ln-input">
        </div>
    </div>

    <div>
        <label class="ln-label">Keterangan</label>
        <textarea name="description" rows="2" class="ln-textarea">{{ old('description', $transfer->description) }}</textarea>
    </div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Update</button>
        <a href="{{ route('transfers.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

@endsection