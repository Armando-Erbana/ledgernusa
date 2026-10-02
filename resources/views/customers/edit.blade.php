@extends('layouts.app')
@section('title', 'Edit Customer')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Edit Customer</h1>
        <p class="ln-page-sub">{{ $customer->name }}</p>
    </div>
    <a href="{{ route('customers.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('customers.update', $customer) }}" class="ln-form-card space-y-4 max-w-2xl">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Nama <span style="color:#C0392B">*</span></label>
            <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="ln-input">
        </div>
        <div>
            <label class="ln-label">Kode</label>
            <input type="text" name="code" value="{{ old('code', $customer->code) }}" class="ln-input">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="ln-input">
        </div>
        <div>
            <label class="ln-label">Telepon</label>
            <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="ln-input">
        </div>
    </div>

    <div>
        <label class="ln-label">NPWP</label>
        <input type="text" name="npwp" value="{{ old('npwp', $customer->npwp) }}" class="ln-input">
    </div>

    <div>
        <label class="ln-label">Alamat</label>
        <textarea name="address" rows="2" class="ln-textarea">{{ old('address', $customer->address) }}</textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="ln-label">Limit Kredit (Rp)</label>
            <input type="number" name="credit_limit" value="{{ old('credit_limit', $customer->credit_limit) }}" min="0" step="1000" class="ln-input">
        </div>
        <div>
            <label class="ln-label">Term Pembayaran (hari)</label>
            <input type="number" name="payment_term_days" value="{{ old('payment_term_days', $customer->payment_term_days) }}" min="0" class="ln-input">
        </div>
    </div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Update</button>
        <a href="{{ route('customers.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

@endsection