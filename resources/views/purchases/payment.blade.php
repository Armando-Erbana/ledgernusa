@extends('layouts.app')
@section('title', 'Bayar Pembelian')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Bayar Hutang</h1>
        <p class="ln-page-sub">{{ $purchase->bill_number }} · Sisa Rp {{ number_format($purchase->balance, 0, ',', '.') }}</p>
    </div>
    <a href="{{ route('purchases.show', $purchase) }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('purchases.payment.store', $purchase) }}" class="ln-form-card space-y-4 max-w-2xl">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="ln-label">Tanggal *</label><input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="ln-input"></div>
        <div>
            <label class="ln-label">Akun Kas/Bank *</label>
            <select name="cash_account_id" required class="ln-select">
                <option value="">- Pilih -</option>
                @foreach($cashAccounts as $a)<option value="{{ $a->id }}" @selected(old('cash_account_id') == $a->id)>{{ $a->code }} - {{ $a->name }}</option>@endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="ln-label">Jumlah Bayar (Rp) *</label>
        <input type="number" name="amount" value="{{ old('amount', $purchase->balance) }}" required min="0.01" max="{{ $purchase->balance }}" step="0.01" class="ln-input">
        <small class="text-gray-500">Maksimal: Rp {{ number_format($purchase->balance, 0, ',', '.') }}</small>
    </div>

    <div>
        <label class="ln-label">PPh 23 Dipotong (Rp)</label>
        <input type="number" name="pph23" value="{{ old('pph23', 0) }}" min="0" max="{{ $purchase->balance }}" step="0.01" class="ln-input">
        <small class="text-gray-500">Isi 0 kalau tidak ada PPh 23</small>
    </div>

    <div><label class="ln-label">Referensi</label><input type="text" name="reference" value="{{ old('reference') }}" class="ln-input"></div>
    <div><label class="ln-label">Catatan</label><textarea name="notes" rows="2" class="ln-textarea">{{ old('notes') }}</textarea></div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Simpan Pembayaran</button>
        <a href="{{ route('purchases.show', $purchase) }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>
@endsection