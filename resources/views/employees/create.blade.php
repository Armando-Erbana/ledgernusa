@extends('layouts.app')
@section('title', 'Karyawan Baru')
@section('content')

<div class="ln-page-head">
    <div><h1 class="ln-page-title">Karyawan Baru</h1></div>
    <a href="{{ route('employees.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('employees.store') }}" class="ln-form-card space-y-4 max-w-3xl">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="ln-label">NIK *</label><input type="text" name="employee_number" value="{{ old('employee_number') }}" required class="ln-input" placeholder="EMP-001"></div>
        <div class="md:col-span-2"><label class="ln-label">Nama *</label><input type="text" name="name" value="{{ old('name') }}" required class="ln-input"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="ln-label">Jabatan</label><input type="text" name="position" value="{{ old('position') }}" class="ln-input"></div>
        <div><label class="ln-label">Departemen</label><input type="text" name="department" value="{{ old('department') }}" class="ln-input"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="ln-label">Tgl Masuk</label><input type="date" name="join_date" value="{{ old('join_date', date('Y-m-d')) }}" class="ln-input"></div>
        <div>
            <label class="ln-label">Tipe Karyawan *</label>
            <select name="employment_type" class="ln-select" required>
                <option value="permanent">Tetap</option>
                <option value="contract">Kontrak</option>
                <option value="freelance">Freelance</option>
            </select>
        </div>
        <div>
            <label class="ln-label">Status PTKP *</label>
            <select name="ptkp_status" class="ln-select" required>
                @foreach(['TK/0','TK/1','TK/2','TK/3','K/0','K/1','K/2','K/3'] as $ptkp)
                    <option value="{{ $ptkp }}" @selected(old('ptkp_status')===$ptkp)>{{ $ptkp }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="ln-label">NPWP</label><input type="text" name="npwp" value="{{ old('npwp') }}" class="ln-input"></div>
        <div><label class="ln-label">KTP</label><input type="text" name="ktp" value="{{ old('ktp') }}" class="ln-input"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="ln-label">Gaji Pokok (Rp) *</label><input type="number" name="basic_salary" value="{{ old('basic_salary', 0) }}" required min="0" step="1000" class="ln-input"></div>
        <div><label class="ln-label">Tunjangan Tetap (Rp)</label><input type="number" name="fixed_allowance" value="{{ old('fixed_allowance', 0) }}" min="0" step="1000" class="ln-input"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="ln-label">Nama Bank</label><input type="text" name="bank_name" value="{{ old('bank_name') }}" class="ln-input"></div>
        <div><label class="ln-label">No. Rekening</label><input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" class="ln-input"></div>
    </div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Simpan</button>
        <a href="{{ route('employees.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

@endsection