@extends('layouts.app')
@section('title', 'Edit Supplier')
@section('content')

<div class="ln-page-head">
    <div><h1 class="ln-page-title">Edit Supplier</h1></div>
    <a href="{{ route('suppliers.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="ln-form-card space-y-4 max-w-2xl">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="ln-label">Nama *</label><input type="text" name="name" value="{{ old('name', $supplier->name) }}" required class="ln-input"></div>
        <div><label class="ln-label">Kode</label><input type="text" name="code" value="{{ old('code', $supplier->code) }}" class="ln-input"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="ln-label">Email</label><input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="ln-input"></div>
        <div><label class="ln-label">Telepon</label><input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="ln-input"></div>
    </div>
    <div><label class="ln-label">NPWP</label><input type="text" name="npwp" value="{{ old('npwp', $supplier->npwp) }}" class="ln-input"></div>
    <div><label class="ln-label">Alamat</label><textarea name="address" rows="2" class="ln-textarea">{{ old('address', $supplier->address) }}</textarea></div>
    <div><label class="ln-label">Term (hari)</label><input type="number" name="payment_term_days" value="{{ old('payment_term_days', $supplier->payment_term_days) }}" min="0" class="ln-input"></div>
    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Update</button>
        <a href="{{ route('suppliers.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>
@endsection