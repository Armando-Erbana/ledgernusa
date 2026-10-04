@extends('layouts.app')
@section('title', 'Karyawan')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Karyawan</h1>
        <p class="ln-page-sub">{{ $employees->total() }} karyawan terdaftar</p>
    </div>
    <a href="{{ route('employees.create') }}" class="ln-btn-primary">+ Karyawan</a>
</div>

<form method="GET" class="ln-form-card mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
    <div><label class="ln-label">Cari</label><input type="text" name="q" value="{{ request('q') }}" class="ln-input" placeholder="Nama..."></div>
    <div>
        <label class="ln-label">Status</label>
        <select name="status" class="ln-select">
            <option value="">Semua</option>
            <option value="active" @selected(request('status')==='active')>Aktif</option>
            <option value="inactive" @selected(request('status')==='inactive')>Non-Aktif</option>
        </select>
    </div>
    <div class="flex items-end"><button class="ln-btn-primary">Filter</button></div>
</form>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr><th>NIK</th><th>Nama</th><th>Jabatan</th><th>PTKP</th><th class="num">Gaji Pokok</th><th class="center">Status</th><th class="center">Aksi</th></tr>
        </thead>
        <tbody>
        @forelse($employees as $e)
            <tr>
                <td><span class="ln-code">{{ $e->employee_number }}</span></td>
                <td><strong>{{ $e->name }}</strong></td>
                <td>{{ $e->position ?: '-' }}</td>
                <td>{{ $e->ptkp_status }}</td>
                <td class="num">Rp {{ number_format($e->basic_salary, 0, ',', '.') }}</td>
                <td class="center"><span class="ln-badge {{ $e->is_active ? 'posted' : 'draft' }}">{{ $e->is_active ? 'Aktif' : 'Non-Aktif' }}</span></td>
                <td class="center">
                    <a href="{{ route('employees.edit', $e) }}" class="ln-action">Edit</a>
                    <span class="ln-action-sep">|</span>
                    <form method="POST" action="{{ route('employees.destroy', $e) }}" class="inline" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button class="ln-action red">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty">Belum ada karyawan</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="ln-pagination">{{ $employees->links() }}</div>

@endsection