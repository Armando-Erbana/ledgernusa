@extends('layouts.app')
@section('title', 'Neraca')

@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Neraca</h1>
        <p class="ln-page-sub">Posisi keuangan pada tanggal tertentu</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('export.balance-sheet-pdf', ['as_of' => $asOf, 'branch_id' => request('branch_id')]) }}" target="_blank" class="ln-btn-outline">
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

<form method="GET" class="ln-form-card mb-6 grid grid-cols-1 md:grid-cols-3 gap-3">
    <div>
        <label class="ln-label">Per Tanggal</label>
        <input type="date" name="as_of" value="{{ $asOf }}" class="ln-input">
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

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    {{-- ASET --}}
    <div class="ln-table-card flex flex-col">
        <div class="p-4 border-b border-[var(--hair)] font-bold text-[var(--ink)]">Aset</div>
        <table class="ln-table flex-1">
            <tbody>
                @forelse($assets as $a)
                    <tr>
                        <td>{{ $a->code }} - {{ $a->name }}</td>
                        <td class="num">Rp {{ number_format($a->balance, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td class="empty text-sm">-</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-[rgba(0,0,0,.02)] font-bold">
                <tr>
                    <td>Total Aset</td>
                    <td class="num text-[var(--ink)]">Rp {{ number_format($totalAssets, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- KEWAJIBAN & EKUITAS --}}
    <div class="space-y-6 flex flex-col">
        <div class="ln-table-card">
            <div class="p-4 border-b border-[var(--hair)] font-bold text-[var(--ink)]">Kewajiban</div>
            <table class="ln-table">
                <tbody>
                    @forelse($liabilities as $l)
                        <tr>
                            <td>{{ $l->code }} - {{ $l->name }}</td>
                            <td class="num">Rp {{ number_format($l->balance, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td class="empty text-sm">-</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-[rgba(0,0,0,.02)] font-bold">
                    <tr>
                        <td>Total Kewajiban</td>
                        <td class="num text-[var(--ink)]">Rp {{ number_format($totalLiabilities, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="ln-table-card flex-1">
            <div class="p-4 border-b border-[var(--hair)] font-bold text-[var(--ink)]">Ekuitas</div>
            <table class="ln-table">
                <tbody>
                    @forelse($equities as $e)
                        <tr>
                            <td>{{ $e->code }} - {{ $e->name }}</td>
                            <td class="num">Rp {{ number_format($e->balance, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td class="empty text-sm">-</td></tr>
                    @endforelse
                    <tr>
                        <td class="italic text-[var(--mute)]">Laba / (Rugi) Berjalan</td>
                        <td class="num italic text-[var(--mute)]">Rp {{ number_format($currentEarnings, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
                <tfoot class="bg-[rgba(0,0,0,.02)] font-bold">
                    <tr>
                        <td>Total Ekuitas</td>
                        <td class="num text-[var(--ink)]">Rp {{ number_format($totalEquity + $currentEarnings, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- TOTAL KESELURUHAN --}}
<div class="ln-form-card flex justify-between items-center bg-[rgba(0,0,0,.02)] mb-4">
    <div class="font-bold text-lg text-[var(--ink)]">Total Kewajiban + Ekuitas</div>
    <div class="text-2xl font-bold text-[var(--ink)]">
        Rp {{ number_format($totalLiabilities + $totalEquity + $currentEarnings, 0, ',', '.') }}
    </div>
</div>

{{-- VALIDASI BALANCE --}}
@php $diff = $totalAssets - ($totalLiabilities + $totalEquity + $currentEarnings); @endphp
<div>
    @if(abs($diff) > 0.01)
        <div class="ln-alert ln-alert-error" role="alert">
            Neraca tidak balance. Selisih: Rp {{ number_format(abs($diff), 0, ',', '.') }}
        </div>
    @else
        <div class="ln-alert ln-alert-success" role="status">
            ✓ Neraca Balance
        </div>
    @endif
</div>

@endsection