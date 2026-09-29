@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Dashboard</h1>
        <p class="ln-page-sub">Ringkasan keuangan {{ auth()->user()->activeCompany()->name ?? 'perusahaan Anda' }}</p>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
    <div class="ln-stat kas">
        <div class="top">
            <span class="lbl">Saldo Kas</span>
            <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6.5" width="18" height="11" rx="2"/><circle cx="12" cy="12" r="2.4"/></svg></span>
        </div>
        <div class="val">Rp {{ number_format($totalKas, 0, ',', '.') }}</div>
    </div>
    <div class="ln-stat bank">
        <div class="top">
            <span class="lbl">Saldo Bank</span>
            <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10 12 4l9 6"/><path d="M5 10v9M10 10v9M14 10v9M19 10v9"/><path d="M3 19h18"/></svg></span>
        </div>
        <div class="val">Rp {{ number_format($totalBank, 0, ',', '.') }}</div>
    </div>
    <div class="ln-stat akun">
        <div class="top">
            <span class="lbl">Total Akun</span>
            <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 20V10M12 20V4M19 20v-7"/></svg></span>
        </div>
        <div class="val">{{ \App\Models\Account::where('company_id', session('company_id'))->count() }}</div>
    </div>
    <div class="ln-stat jurnal">
        <div class="top">
            <span class="lbl">Total Jurnal</span>
            <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4.5h11a3 3 0 0 1 3 3V20H8a3 3 0 0 1-3-3z"/><path d="M9 9h7M9 12.5h7"/></svg></span>
        </div>
        <div class="val">{{ \App\Models\Journal::where('company_id', session('company_id'))->count() }}</div>
    </div>
</div>

<div class="flex flex-wrap gap-2 mb-9">
    <a href="{{ route('journals.create') }}" class="ln-btn-primary">+ Jurnal Baru</a>
    <a href="{{ route('accounts.create') }}" class="ln-btn-outline">+ Akun COA</a>
    <a href="{{ route('contacts.create') }}" class="ln-btn-outline">+ Kontak</a>
</div>

<h2 class="ln-section-title">Jurnal Terbaru</h2>
<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Referensi</th>
                <th class="num">Total</th>
                <th class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentJournals as $j)
                <tr>
                    <td>{{ $j->date->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('journals.show', $j) }}" class="ref">
                            {{ $j->reference ?: '#' . $j->id }}
                        </a>
                    </td>
                    <td class="num">Rp {{ number_format($j->totalDebit(), 0, ',', '.') }}</td>
                    <td class="center">
                        <span class="ln-badge {{ $j->status === 'posted' ? 'posted' : 'draft' }}">
                            {{ $j->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">Belum ada jurnal</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection