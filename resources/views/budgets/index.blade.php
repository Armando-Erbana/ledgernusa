@extends('layouts.app')
@section('title', 'Budgeting')

@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Budgeting & Forecast</h1>
        <p class="ln-page-sub">Rencanakan anggaran pendapatan dan beban perusahaan</p>
    </div>
</div>

@if(!$branchId)
    <div class="ln-alert ln-alert-error mb-4" role="alert">
        Anda sedang berada di mode Konsolidasi. Silakan pilih cabang spesifik di pojok kanan atas untuk melihat atau menginput Budget.
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2">
        <div class="ln-form-card mb-4">
            <form method="GET" class="flex items-end gap-3">
                <div>
                    <label class="ln-label">Bulan</label>
                    <select name="month" class="ln-select" onchange="this.form.submit()">
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" @selected($month == $i)>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="ln-label">Tahun</label>
                    <input type="number" name="year" value="{{ $year }}" class="ln-input" onchange="this.form.submit()">
                </div>
            </form>
        </div>

        <div class="ln-table-card">
            <table class="ln-table">
                <thead>
                    <tr>
                        <th>Kode Akun</th>
                        <th>Nama Akun</th>
                        <th class="num">Nominal Budget</th>
                        <th class="center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budgets as $b)
                        <tr>
                            <td class="ln-code">{{ $b->account->code }}</td>
                            <td class="font-semibold text-[var(--ink)]">{{ $b->account->name }}</td>
                            <td class="num">Rp {{ number_format($b->amount, 0, ',', '.') }}</td>
                            <td class="center">
                                <form action="{{ route('budgets.destroy', $b) }}" method="POST" class="inline" onsubmit="return confirm('Hapus budget ini?')">
                                    @csrf @method('DELETE')
                                    <button class="ln-action red">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty">Belum ada budget untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FORM INPUT BUDGET --}}
    <div>
        <form method="POST" action="{{ route('budgets.store') }}" class="ln-form-card space-y-4">
            @csrf
            <div class="ln-section-title">Input Budget Baru</div>
            
            <div>
                <label class="ln-label">Pilih Akun (Beban / Pendapatan) *</label>
                <select name="account_id" class="ln-select" required>
                    <option value="">- Pilih Akun -</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" @selected(old('account_id') == $acc->id)>
                            {{ $acc->code }} - {{ $acc->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="period_month" value="{{ $month }}">
            <input type="hidden" name="period_year" value="{{ $year }}">

            <div>
                <label class="ln-label">Nominal Budget (Rp) *</label>
                <input type="number" name="amount" required class="ln-input" placeholder="Contoh: 5000000">
                <p class="text-xs text-[var(--mute)] mt-1">Untuk periode {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</p>
            </div>

            <button class="ln-btn-primary w-full justify-center" {{ !$branchId ? 'disabled' : '' }}>Simpan Budget</button>
        </form>
    </div>
</div>

@endsection