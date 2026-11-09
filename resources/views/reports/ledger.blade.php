@extends('layouts.app')
@section('title', 'Buku Besar')

@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Buku Besar</h1>
        <p class="ln-page-sub">Riwayat mutasi transaksi per akun</p>
    </div>
</div>

{{-- LOGIKA FILTER CABANG --}}
@php
    $planCode = strtolower(auth()->user()->activeCompany()->subscription?->plan?->code ?? 'starter');
    $isPro = in_array($planCode, ['pro', 'enterprise']);
    $branches = $isPro ? \App\Models\Branch::where('company_id', session('company_id'))->get() : collect();
    $currentBranch = request('branch_id', session('branch_id') ?? 'all');
@endphp

<form method="GET" class="ln-form-card mb-6 grid grid-cols-1 md:grid-cols-5 gap-3">
    <div>
        <label class="ln-label">Akun *</label>
        <select name="account_id" class="ln-select" required>
            <option value="">- Pilih Akun -</option>
            @foreach($accounts as $a)
                <option value="{{ $a->id }}" @selected($account && $account->id == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
            @endforeach
        </select>
    </div>
    
    <div>
        <label class="ln-label">Dari Tanggal</label>
        <input type="date" name="from" value="{{ $from }}" class="ln-input">
    </div>
    
    <div>
        <label class="ln-label">Sampai Tanggal</label>
        <input type="date" name="to" value="{{ $to }}" class="ln-input">
    </div>

    {{-- Filter Cabang (Hanya Muncul untuk Pro/Enterprise) --}}
    @if($isPro && $branches->count() > 0)
    <div>
        <label class="ln-label">Tampilkan Data</label>
        <select name="branch_id" class="ln-select">
            <option value="all" {{ $currentBranch === 'all' ? 'selected' : '' }}>Konsolidasi (Semua Cabang)</option>
            @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ $currentBranch == $b->id ? 'selected' : '' }}>
                    Cabang {{ $b->name }}
                </option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="flex items-end">
        <button class="ln-btn-primary w-full justify-center">Tampilkan</button>
    </div>
</form>

@if($account)
    <div class="ln-form-card mb-4 flex items-center justify-between">
        <div>
            <div class="font-bold text-lg text-[var(--ink)]">{{ $account->code }} - {{ $account->name }}</div>
            <div class="text-xs text-[var(--mute)] mt-1 uppercase tracking-wider font-semibold">Normal Balance: {{ $account->normal_balance }}</div>
        </div>
        @if($currentBranch && $currentBranch !== 'all')
            <div class="ln-badge posted">Data Cabang Terfilter</div>
        @elseif($isPro)
            <div class="ln-badge draft">Data Konsolidasi</div>
        @endif
    </div>

    <div class="ln-table-card">
        <table class="ln-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Referensi</th>
                    <th>Deskripsi</th>
                    <th class="num">Debit</th>
                    <th class="num">Kredit</th>
                    <th class="num">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-[rgba(0,0,0,.02)]">
                    <td colspan="5" class="text-right font-bold text-[var(--soft)]">Saldo Awal</td>
                    <td class="num font-bold text-[var(--ink)]">{{ number_format($openingBalance, 0, ',', '.') }}</td>
                </tr>
                
                @php $running = $openingBalance; @endphp
                @forelse($entries as $e)
                    @php
                        $running += $account->normal_balance === 'debit'
                            ? ($e->debit - $e->credit)
                            : ($e->credit - $e->debit);
                    @endphp
                    <tr>
                        <td class="whitespace-nowrap">{{ $e->journal->date->format('d/m/Y') }}</td>
                        <td>{{ $e->journal->reference ?: '#' . $e->journal->id }}</td>
                        <td class="text-[var(--mute)]">{{ $e->description ?: $e->journal->description }}</td>
                        <td class="num">{{ $e->debit > 0 ? number_format($e->debit, 0, ',', '.') : '-' }}</td>
                        <td class="num">{{ $e->credit > 0 ? number_format($e->credit, 0, ',', '.') : '-' }}</td>
                        <td class="num">{{ number_format($running, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Tidak ada mutasi pada periode ini.</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-[rgba(0,0,0,.02)] font-bold">
                <tr>
                    <td colspan="5" class="text-right">Saldo Akhir</td>
                    <td class="num">{{ number_format($closingBalance, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
@else
    <div class="ln-form-card text-center py-12">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <p class="text-[var(--mute)] text-sm">Silakan pilih akun terlebih dahulu untuk menampilkan Buku Besar.</p>
    </div>
@endif

@endsection