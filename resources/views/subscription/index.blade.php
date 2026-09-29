@extends('layouts.app')
@section('title', 'Langganan')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Langganan</h1>
        <p class="ln-page-sub">Kelola paket & tagihan Anda</p>
    </div>
</div>

@if($subscription)
    <div class="ln-form-card mb-4">
        <div class="flex justify-between items-start">
            <div>
                <div class="text-xs text-gray-500">Paket Saat Ini</div>
                <div class="text-2xl font-bold ln-display">{{ $subscription->plan->name ?? '-' }}</div>
                <div class="text-sm text-gray-600 mt-1">
                    Status:
                    <span class="ln-badge {{ $subscription->status === 'active' ? 'posted' : 'draft' }}">
                        {{ $subscription->status }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                @if($subscription->status === 'trial')
                    <div class="text-sm text-gray-500">Sisa trial</div>
                    <div class="text-3xl font-bold text-yellow-600">{{ $subscription->daysRemaining() }} hari</div>
                @else
                    <div class="text-sm text-gray-500">Berakhir</div>
                    <div class="text-lg font-semibold">{{ $subscription->ends_at?->format('d M Y') }}</div>
                @endif
            </div>
        </div>
    </div>
@endif

<h2 class="ln-section-title">Paket Tersedia</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
    @foreach($plans as $plan)
        <div class="ln-form-card {{ $subscription && $subscription->plan_id === $plan->id ? 'border-2 border-indigo-600' : '' }}">
            <div class="text-xs text-indigo-600 font-semibold mb-1">{{ $plan->name }}</div>
            <div class="text-2xl font-bold ln-display mb-3">Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}<span class="text-sm font-normal text-gray-500">/bulan</span></div>
            <div class="text-xs text-gray-600 space-y-1">
                <div>• {{ $plan->max_users == -1 ? 'Unlimited' : $plan->max_users }} user</div>
                <div>• {{ $plan->max_journals_per_month == -1 ? 'Unlimited' : number_format($plan->max_journals_per_month) }} jurnal/bulan</div>
                <div>• {{ $plan->max_companies == -1 ? 'Unlimited' : $plan->max_companies }} company</div>
            </div>
            <button class="ln-btn-primary w-full justify-center mt-4" onclick="alert('Fitur upgrade akan segera hadir. Hubungi admin untuk upgrade manual.')">
                {{ $subscription && $subscription->plan_id === $plan->id ? 'Paket Aktif' : 'Pilih Paket' }}
            </button>
        </div>
    @endforeach
</div>

@if($invoices->count() > 0)
    <h2 class="ln-section-title">Riwayat Tagihan</h2>
    <div class="ln-table-card">
        <table class="ln-table">
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Tanggal</th>
                    <th class="num">Jumlah</th>
                    <th class="center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                    <tr>
                        <td class="ln-code">{{ $inv->invoice_number }}</td>
                        <td>{{ $inv->created_at->format('d/m/Y') }}</td>
                        <td class="num">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                        <td class="center">
                            <span class="ln-badge {{ $inv->status === 'paid' ? 'posted' : 'draft' }}">{{ $inv->status }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection