@extends('super-admin.layout')
@section('title', 'Super Admin Dashboard')

@section('content')

<div class="sa-head">
    <h1 class="sa-title">Dashboard</h1>
    <p class="sa-sub">Ringkasan bisnis LedgerNusa</p>
</div>

{{-- Stat cards --}}
<div class="sa-grid-stats">
    <div class="sa-stat cyan">
        <div class="lbl">Total Tenant</div>
        <div class="val">{{ number_format($totalCompanies) }}</div>
        <div class="sub">Perusahaan terdaftar</div>
    </div>
    <div class="sa-stat navy">
        <div class="lbl">Total User</div>
        <div class="val">{{ number_format($totalUsers) }}</div>
        <div class="sub">Exclude super admin</div>
    </div>
    <div class="sa-stat green">
        <div class="lbl">Langganan Aktif</div>
        <div class="val">{{ $activeCount }}</div>
        <div class="sub">{{ $trialCount }} trial · {{ $expiredCount }} expired</div>
    </div>
    <div class="sa-stat yellow">
        <div class="lbl">Estimasi MRR</div>
        <div class="val">Rp {{ number_format($mrr, 0, ',', '.') }}</div>
        <div class="sub">Dari langganan aktif</div>
    </div>
</div>

{{-- Expiring soon --}}
@if($expiringSoon->count() > 0)
    <div class="sa-card">
        <div class="sa-card-head">
            <div class="sa-card-title">⏰ Akan Berakhir 7 Hari ke Depan</div>
            <span class="sa-badge trial">{{ $expiringSoon->count() }} tenant</span>
        </div>
        <table class="sa-table">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Paket</th>
                    <th>Status</th>
                    <th>Berakhir</th>
                    <th>Sisa</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($expiringSoon as $sub)
                    <tr>
                        <td><strong>{{ $sub->company->name ?? '-' }}</strong></td>
                        <td>{{ $sub->plan->name ?? '-' }}</td>
                        <td><span class="sa-badge {{ $sub->status }}">{{ $sub->status }}</span></td>
                        <td>{{ $sub->ends_at?->format('d M Y') }}</td>
                        <td>{{ $sub->daysRemaining() }} hari</td>
                        <td>
                            @if($sub->company)
                                <a href="{{ route('super-admin.tenants.show', $sub->company) }}" class="sa-btn sa-btn-cyan sa-btn-sm">Kelola</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

{{-- Recent tenants --}}
<div class="sa-card">
    <div class="sa-card-head">
        <div class="sa-card-title">Tenant Terbaru</div>
        <a href="{{ route('super-admin.tenants.index') }}" class="sa-btn sa-btn-outline sa-btn-sm">Lihat Semua →</a>
    </div>
    <table class="sa-table">
        <thead>
            <tr>
                <th>Company</th>
                <th>Owner</th>
                <th>Paket</th>
                <th>Status</th>
                <th>Daftar</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentCompanies as $company)
                @php
                    $owner = $company->users->where('pivot.role', 'owner')->first();
                    $sub = $company->subscription;
                @endphp
                <tr>
                    <td><strong>{{ $company->name }}</strong></td>
                    <td>{{ $owner->name ?? '-' }}<br><span style="font-size:11.5px; color:#7A84A0;">{{ $owner->email ?? '' }}</span></td>
                    <td>{{ $sub->plan->name ?? '—' }}</td>
                    <td>
                        @if($sub)
                            <span class="sa-badge {{ $sub->status }}">{{ $sub->status }}</span>
                        @else
                            <span style="color:#96A1BC;">—</span>
                        @endif
                    </td>
                    <td style="font-size:12.5px; color:#7A84A0;">{{ $company->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('super-admin.tenants.show', $company) }}" class="sa-btn sa-btn-outline sa-btn-sm">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding: 40px; color:#96A1BC;">Belum ada tenant</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection