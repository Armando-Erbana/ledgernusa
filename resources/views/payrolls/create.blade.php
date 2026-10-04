@extends('layouts.app')
@section('title', 'Payroll Baru')
@section('content')

@php
    $employeesJson = $employees->map(fn($e) => [
        'id' => $e->id,
        'number' => $e->employee_number,
        'name' => $e->name,
        'basic' => (float) $e->basic_salary,
        'allowance' => (float) $e->fixed_allowance,
    ]);
@endphp

<div class="ln-page-head">
    <div><h1 class="ln-page-title">Payroll Baru</h1></div>
    <a href="{{ route('payrolls.index') }}" class="ln-action">← Kembali</a>
</div>

<form method="POST" action="{{ route('payrolls.store') }}" class="ln-form-card space-y-4">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div><label class="ln-label">No. Payroll *</label><input type="text" name="payroll_number" value="{{ old('payroll_number', $nextNumber) }}" required class="ln-input"></div>
        <div>
            <label class="ln-label">Bulan *</label>
            <select name="period_month" class="ln-select" required>
                @for($i=1; $i<=12; $i++)
                    <option value="{{ $i }}" @selected($i == date('n'))>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                @endfor
            </select>
        </div>
        <div><label class="ln-label">Tahun *</label><input type="number" name="period_year" value="{{ date('Y') }}" required class="ln-input"></div>
        <div><label class="ln-label">Tgl Bayar *</label><input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="ln-input"></div>
    </div>

    <div>
        <label class="ln-label">Daftar Karyawan</label>
        <div class="overflow-x-auto">
            <table class="ln-entries-table">
                <thead>
                    <tr>
                        <th style="width:40px" class="center"><input type="checkbox" id="checkAll" onchange="toggleAll(this)"></th>
                        <th>Karyawan</th>
                        <th class="num" style="width:130px">Gaji Pokok</th>
                        <th class="num" style="width:130px">Tunjangan</th>
                        <th class="num" style="width:130px">Lain-lain</th>
                        <th class="num" style="width:130px">Potongan Lain</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($employees as $i => $e)
                    <tr>
                        <td class="center"><input type="checkbox" class="row-check" onchange="toggleRow(this)"></td>
                        <td>
                            <strong>{{ $e->name }}</strong><br><small class="text-gray-500">{{ $e->employee_number }} · PTKP {{ $e->ptkp_status }}</small>
                            <input type="hidden" name="items[{{ $i }}][employee_id]" value="{{ $e->id }}" disabled>
                        </td>
                        <td><input type="number" name="items[{{ $i }}][basic_salary]" value="{{ (float) $e->basic_salary }}" class="ln-input ln-input-sm" disabled style="text-align:right"></td>
                        <td><input type="number" name="items[{{ $i }}][allowance]" value="{{ (float) $e->fixed_allowance }}" class="ln-input ln-input-sm" disabled style="text-align:right"></td>
                        <td><input type="number" name="items[{{ $i }}][other_income]" value="0" class="ln-input ln-input-sm" disabled style="text-align:right"></td>
                        <td><input type="number" name="items[{{ $i }}][other_deduction]" value="0" class="ln-input ln-input-sm" disabled style="text-align:right"></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3 text-sm text-gray-500">
            PPh 21 & BPJS akan dihitung otomatis saat simpan.
        </div>
    </div>

    <div><label class="ln-label">Catatan</label><textarea name="notes" rows="2" class="ln-textarea"></textarea></div>

    <div class="ln-form-actions pt-3">
        <button class="ln-btn-primary">Simpan Payroll</button>
        <a href="{{ route('payrolls.index') }}" class="ln-btn-cancel">Batal</a>
    </div>
</form>

<script>
function toggleRow(cb) {
    const tr = cb.closest('tr');
    tr.querySelectorAll('input').forEach(el => el.disabled = !cb.checked);
}

function toggleAll(cb) {
    document.querySelectorAll('.row-check').forEach((el, i) => {
        el.checked = cb.checked;
        toggleRow(el);
    });
}
</script>

@endsection