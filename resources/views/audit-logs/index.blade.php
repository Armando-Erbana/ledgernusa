    @extends('layouts.app')
@section('title', 'Audit Trail')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Audit Trail</h1>
        <p class="ln-page-sub">Riwayat aktivitas user di sistem</p>
    </div>
</div>

<form method="GET" class="ln-form-card mb-4 grid grid-cols-1 md:grid-cols-5 gap-3">
    <div>
        <label class="ln-label">User</label>
        <select name="user_id" class="ln-select">
            <option value="">Semua</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="ln-label">Aksi</label>
        <select name="action" class="ln-select">
            <option value="">Semua</option>
            @foreach(['create'=>'Create','update'=>'Update','delete'=>'Delete'] as $k=>$v)
                <option value="{{ $k }}" @selected(request('action')===$k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="ln-label">Tabel</label>
        <select name="table_name" class="ln-select">
            <option value="">Semua</option>
            @foreach($tables as $t)
                <option value="{{ $t }}" @selected(request('table_name')===$t)>{{ $t }}</option>
            @endforeach
        </select>
    </div>
    <div><label class="ln-label">Dari</label><input type="date" name="from" value="{{ request('from') }}" class="ln-input"></div>
    <div><label class="ln-label">Sampai</label><input type="date" name="to" value="{{ request('to') }}" class="ln-input"></div>
    <div class="md:col-span-5 flex gap-2">
        <button class="ln-btn-primary">Filter</button>
        @if(request()->hasAny(['user_id','action','table_name','from','to']))
            <a href="{{ route('audit-logs.index') }}" class="ln-btn-outline">Reset</a>
        @endif
    </div>
</form>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>User</th>
                <th>Aksi</th>
                <th>Tabel</th>
                <th>Record</th>
                <th class="center">Detail</th>
            </tr>
        </thead>
        <tbody>
        @forelse($logs as $log)
            <tr>
                <td class="whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $log->user->name ?? '-' }}</td>
                <td>
                    @php
                        $badge = match($log->action) {
                            'create' => 'posted',
                            'update' => 'draft',
                            'delete' => 'unpaid',
                            default => 'draft',
                        };
                    @endphp
                    <span class="ln-badge {{ $badge }}">{{ $log->action }}</span>
                </td>
                <td><span class="ln-code">{{ $log->table_name }}</span></td>
                <td>#{{ $log->record_id }}</td>
                <td class="center"><a href="{{ route('audit-logs.show', $log) }}" class="ln-action">Lihat</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">Belum ada aktivitas</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="ln-pagination">{{ $logs->links() }}</div>

@endsection