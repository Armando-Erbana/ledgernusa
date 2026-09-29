@extends('super-admin.layout')
@section('title', 'Daftar Tenant')

@section('content')

<div class="sa-head">
    <h1 class="sa-title">Tenants</h1>
    <p class="sa-sub">Semua perusahaan yang menggunakan LedgerNusa</p>
</div>

<form method="GET" style="background:#fff; border:1px solid #E1E4EC; border-radius:12px; padding:16px; margin-bottom:20px; display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
    <div style="flex:1; min-width:200px;">
        <label class="sa-label">Cari</label>
        <input type="text" name="q" value="{{ request('q') }}" class="sa-input" placeholder="Nama company...">
    </div>
    <div style="min-width:160px;">
        <label class="sa-label">Status</label>
        <select name="status" class="sa-select">
            <option value="">Semua</option>
            @foreach(['trial','active','expired','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <button class="sa-btn sa-btn-primary">Filter</button>
    @if(request()->hasAny(['q','status']))
        <a href="{{ route('super-admin.tenants.index') }}" class="sa-btn sa-btn-outline">Reset</a>
    @endif
</form>

<div class="sa-card">
    <table class="sa-table">
        <thead>
            <tr>
                <th>Company</th>
                <th>Owner</th>
                <th>Paket</th>
                <th>Status</th>
                <th>Berakhir</th>
                <th>User</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tenants as $tenant)
                @php
                    $owner = $tenant->users->where('pivot.role', 'owner')->first();
                    $sub = $tenant->subscription;
                @endphp
                <tr>
                    <td><strong>{{ $tenant->name }}</strong></td>
                    <td>{{ $owner->name ?? '-' }}<br><span style="font-size:11.5px; color:#7A84A0;">{{ $owner->email ?? '' }}</span></td>
                    <td>{{ $sub->plan->name ?? '—' }}</td>
                    <td>
                        @if($sub)
                            <span class="sa-badge {{ $sub->status }}">{{ $sub->status }}</span>
                        @else
                            <span style="color:#96A1BC;">—</span>
                        @endif
                    </td>
                    <td style="font-size:12.5px;">{{ $sub?->ends_at?->format('d M Y') ?? '—' }}</td>
                    <td>{{ $tenant->users->count() }}</td>
                    <td><a href="{{ route('super-admin.tenants.show', $tenant) }}" class="sa-btn sa-btn-outline sa-btn-sm">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center; padding: 40px; color:#96A1BC;">Tidak ada tenant</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:18px;">{{ $tenants->withQueryString()->links() }}</div>

@endsection