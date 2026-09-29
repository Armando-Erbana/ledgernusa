@extends('layouts.app')
@section('title', 'Akun Baru')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Akun Baru</h1>
        <p class="ln-page-sub">Tambahkan akun baru ke chart of accounts</p>
    </div>
</div>

<form method="POST" action="{{ route('accounts.store') }}" class="ln-form-card space-y-4">
    @csrf
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Kode</label>
            <input type="text" name="code" value="{{ old('code') }}" required class="ln-input">
        </div>
        <div>
            <label class="ln-label">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="ln-input">
        </div>
    </div>
    <div>
        <label class="ln-label">Tipe</label>
        <select name="type" required class="ln-select">
            <option value="asset">Asset</option>
            <option value="liability">Liability</option>
            <option value="equity">Equity</option>
            <option value="revenue">Revenue</option>
            <option value="expense">Expense</option>
        </select>
    </div>
    <div>
        <label class="ln-label">Parent (opsional)</label>
        <select name="parent_id" class="ln-select">
            <option value="">- Tidak ada -</option>
            @foreach($parents as $p)
                <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-5">
        <label class="ln-checkbox"><input type="checkbox" name="is_cash" value="1"> Kas</label>
        <label class="ln-checkbox"><input type="checkbox" name="is_bank" value="1"> Bank</label>
    </div>
    <div class="ln-form-actions">
        <button class="ln-btn-primary">Simpan</button>
        <a href="{{ route('accounts.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

@endsection