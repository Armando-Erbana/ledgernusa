@extends('layouts.app')
@section('title', 'Detail Audit')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Detail Aktivitas</h1>
        <p class="ln-page-sub">{{ $auditLog->created_at->format('d M Y H:i:s') }}</p>
    </div>
    <a href="{{ route('audit-logs.index') }}" class="ln-action">← Kembali</a>
</div>

<div class="ln-form-card mb-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div><div class="ln-label">User</div><div>{{ $auditLog->user->name ?? '-' }}</div></div>
        <div><div class="ln-label">Aksi</div><span class="ln-badge {{ $auditLog->action === 'create' ? 'posted' : ($auditLog->action === 'delete' ? 'unpaid' : 'draft') }}">{{ $auditLog->action }}</span></div>
        <div><div class="ln-label">Tabel</div><div class="ln-code">{{ $auditLog->table_name }}</div></div>
        <div><div class="ln-label">Record ID</div><div>#{{ $auditLog->record_id }}</div></div>
        <div><div class="ln-label">IP</div><div>{{ $auditLog->ip ?: '-' }}</div></div>
        <div class="col-span-3"><div class="ln-label">User Agent</div><div class="text-xs text-gray-500">{{ $auditLog->user_agent ?: '-' }}</div></div>
    </div>
</div>

@if($auditLog->old_values)
    <h2 class="ln-section-title">Nilai Sebelum</h2>
    <div class="ln-table-card mb-4">
        <table class="ln-table">
            <thead><tr><th>Field</th><th>Nilai</th></tr></thead>
            <tbody>
            @foreach($auditLog->old_values as $key => $value)
                <tr>
                    <td><span class="ln-code">{{ $key }}</span></td>
                    <td>{{ is_array($value) ? json_encode($value) : ($value ?? '-') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if($auditLog->new_values)
    <h2 class="ln-section-title">Nilai Sesudah</h2>
    <div class="ln-table-card">
        <table class="ln-table">
            <thead><tr><th>Field</th><th>Nilai</th></tr></thead>
            <tbody>
            @foreach($auditLog->new_values as $key => $value)
                <tr>
                    <td><span class="ln-code">{{ $key }}</span></td>
                    <td>{{ is_array($value) ? json_encode($value) : ($value ?? '-') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection