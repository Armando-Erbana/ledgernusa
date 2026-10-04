@extends('layouts.app')
@section('title', 'Edit Karyawan')
@section('content')

<div class="ln-page-head">
    <div><h1 class="ln-page-title">Edit Karyawan</h1><p class="ln-page-sub">{{ $employee->name }}</p></div>
    <a href="{{ route('employees.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('employees.update', $employee) }}" class="ln-form-card space-y-4 max-w-3xl">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="ln-label">NIK *</label><input type="text" name="employee_number" value="{{ old('employee_number', $employee->employee_number) }}" required class="ln-input"></div>
        <div class="md:col-span-2"><label class="ln-label">Nama *</label><input type="text" name="name" value="{{ old('name', $employee->name) }}" required class="ln-input"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div><label class="ln-label">Jabatan</label><input type="text" name="position" value="{{ old('position', $employee->position) }}" class="ln-input"></div>
        <div><label class="ln-label">Departemen</label><input type="text" name="department" value="{{ old('department', $employee->department) }}" class="ln-input"></div>
        <div>
            <label class="ln-label">Tipe Karyawan</label>
            <select name="employment_type" class="ln-select">
                @foreach(['permanent'=>'Tetap','contract'=>'Kontrak','freelance'=>'Freelance'] as $k=>$v)
                    <option value="{{ $k }}" @selected($employee->employment_type===$k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="ln-label">Tgl Masuk</label><input type="date" name="join_date" value="{{ old('join_date', $employee->join_date?->format('Y-m-d')) }}" class="ln-input"></div>
        <div><label class="ln-label">Tgl Resign</label><input type="date" name="resign_date" value="{{ old('resign_date', $employee->resign_date?->format('Y-m-d')) }}" class="ln-input"></div>
    </div>

    <div>
        <label class="ln-label">Status PTKP</label>
        <select name="ptkp_status" class="ln-select">
            @foreach(['TK/0','TK/1','TK/2','TK/3','K/0','K/1','K/2','K/3'] as $ptkp)
                <option value="{{ $ptkp }}" @selected($employee->ptkp_status===$ptkp)>{{ $ptkp }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><label class="ln-label">Gaji Pokok</label><input type="number" name="basic_salary" value="{{ old('basic_salary', $employee->basic_salary) }}" min="0" step="1000" class="ln-input"></div>
        <div><label class="ln-label">Tunjangan Tetap</label><input type="number" name="fixed_allowance" value="{{ old('fixed_allowance', $employee->fixed_allowance) }}" min="0" step="1000" class="ln-input"></div>
    </div>

    <div><label class="ln-checkbox"><input type="checkbox" name="is_active" value="1" @checked($employee->is_active)> Karyawan Aktif</label></div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Update</button>
        <a href="{{ route('employees.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

@endsection