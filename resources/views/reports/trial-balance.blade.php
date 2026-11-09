@extends('layouts.app')
@section('title', 'Neraca Saldo')

@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Neraca Saldo</h1>
        <p class="ln-page-sub">Ringkasan saldo akhir seluruh akun pada periode tertentu</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('export.trial-balance-pdf', ['from' => $from, 'to' => $to, 'branch_id' => request('branch_id')]) }}" target="_blank" class="ln-btn-outline">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="mr-1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export PDF
        </a>
    </div>
</div>

@php
    $planCode = strtolower(auth()->user()->activeCompany()->subscription?->plan?->code ?? 'starter');
    $isPro = in_array($planCode, ['pro', 'enterprise']);
    $branches = $isPro ? \App\Models\Branch::where('company_id', session('company_id'))->get() : collect();
    $currentBranch = request('branch_id', session('branch_id') ?? 'all');
@endphp

<form method="GET" class="ln-form-card mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
    <div>
        <label class="ln-label">Dari Tanggal</label>
        <input type="date" name="from" value="{{ $from }}" class="ln-input">
    </div>
    <div>
        <label class="ln-label">Sampai Tanggal</label>
        <input type="date" name="to" value="{{ $to }}" class="ln-input">
    </div>
    
    @if($isPro && $branches->count() > 0)
    <div>
        <label class="ln-label">Tampilkan Data</label>
        <select name="branch_id" class="ln-select">
            <option value="all" {{ $currentBranch === 'all' ? 'selected' : '' }}>Konsolidasi (Semua Cabang)</option>
            @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ $currentBranch == $b->id ? 'selected' : '' }}>Cabang {{ $b->name }}</option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="flex items-end {{ !$isPro ? 'md:col-span-2' : '' }}">
        <button class="ln-btn-primary w-full justify-center">Tampilkan</button>
    </div>
</form>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Akun</th>
                <th class="num">Debit</th>
                <th class="num">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $r)
                <tr>
                    <td class="ln-code">{{ $r['account']->code }}</td>
                    <td class="font-semibold text-[var(--ink)]">{{ $r['account']->name }}</td>
                    <td class="num">{{ $r['debit'] > 0 ? number_format($r['debit'], 0, ',', '.') : '-' }}</td>
                    <td class="num">{{ $r['credit'] > 0 ? number_format($r['credit'], 0, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">Tidak ada data pada periode ini.</td></tr>
            @endforelse
        </tbody>
        <tfoot class="bg-[rgba(0,0,0,.02)] font-bold text-[var(--ink)]">
            <tr>
                <td colspan="2" class="text-right">TOTAL</td>
                <td class="num">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                <td class="num">{{ number_format($totalCredit, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- VALIDASI BALANCE --}}
<div class="mt-4">
    @if(abs($totalDebit - $totalCredit) > 0.01)
        <div class="ln-alert ln-alert-error" role="alert">
            Total debit dan kredit tidak balance. Periksa jurnal Anda.
        </div>
    @else
        <div class="ln-alert ln-alert-success" role="status">
            ✓ Balance
        </div>
    @endif
</div>

@endsection