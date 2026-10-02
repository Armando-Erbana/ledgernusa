@extends('layouts.app')
@section('title', $type === 'in' ? 'Kas Masuk' : 'Kas Keluar')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">{{ $type === 'in' ? 'Kas Masuk' : 'Kas Keluar' }}</h1>
        <p class="ln-page-sub">Jurnal akan otomatis dibuat</p>
    </div>
    <a href="{{ route('cash.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('cash.store') }}" class="ln-form-card space-y-4 max-w-2xl">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">

    {{-- Tanggal & Referensi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Tanggal <span style="color:#C0392B">*</span></label>
            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="ln-input">
        </div>
        <div>
            <label class="ln-label">No. Bukti</label>
            <input type="text" name="reference" value="{{ old('reference') }}" class="ln-input" placeholder="Opsional">
        </div>
    </div>

    {{-- Akun Kas --}}
    <div>
        <label class="ln-label">Akun Kas/Bank <span style="color:#C0392B">*</span></label>
        <select name="cash_account_id" required class="ln-select">
            <option value="">- Pilih Akun -</option>
            @foreach($cashAccounts as $a)
                <option value="{{ $a->id }}" @selected(old('cash_account_id') == $a->id)>
                    {{ $a->code }} - {{ $a->name }}
                    @if($a->is_bank) [Bank] @elseif($a->is_cash) [Kas] @endif
                </option>
            @endforeach
        </select>
    </div>

    {{-- Akun Lawan --}}
    <div>
        <label class="ln-label">
            {{ $type === 'in' ? 'Sumber (Akun Kredit)' : 'Tujuan (Akun Debit)' }}
            <span style="color:#C0392B">*</span>
        </label>
        <select name="counter_account_id" required class="ln-select">
            <option value="">- Pilih Akun -</option>
            @foreach($allAccounts as $a)
                <option value="{{ $a->id }}" @selected(old('counter_account_id') == $a->id)>
                    {{ $a->code }} - {{ $a->name }} ({{ ucfirst($a->type) }})
                </option>
            @endforeach
        </select>
    </div>

    {{-- Jumlah --}}
    <div>
        <label class="ln-label">Jumlah (Rp) <span style="color:#C0392B">*</span></label>
        <input type="number" name="amount" value="{{ old('amount') }}" required min="0.01" step="0.01" class="ln-input" placeholder="0">
    </div>

    {{-- Kontak --}}
    <div>
        <label class="ln-label">Kontak (opsional)</label>
        <select name="contact_id" class="ln-select">
            <option value="">- Tidak ada -</option>
            @foreach($contacts as $c)
                <option value="{{ $c->id }}" @selected(old('contact_id') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Deskripsi --}}
    <div>
        <label class="ln-label">Keterangan</label>
        <textarea name="description" rows="2" class="ln-textarea" placeholder="Misal: Penjualan tunai">{{ old('description') }}</textarea>
    </div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Simpan & Buat Jurnal</button>
        <a href="{{ route('cash.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

@endsection