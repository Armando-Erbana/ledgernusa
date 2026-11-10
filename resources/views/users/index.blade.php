@extends('layouts.app')
@section('title', 'Kelola User')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Kelola User</h1>
        <p class="ln-page-sub">Tambah atau hapus akses staf ke perusahaan Anda</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="md:col-span-2">
        <h2 class="ln-section-title">Daftar User</h2>
        <div class="ln-table-card">
            <table class="ln-table">
                <thead>
                    <tr><th>Nama</th><th>Email</th><th>Role</th><th class="center">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td><span class="ln-badge {{ $u->pivot->role === 'owner' ? 'posted' : 'draft' }}">{{ ucfirst($u->pivot->role) }}</span></td>
                        <td class="center">
                            @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $u) }}" class="inline" onsubmit="return confirm('Hapus user ini dari perusahaan?')">
                                    @csrf @method('DELETE')
                                    <button class="ln-action red">Hapus</button>
                                </form>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">Belum ada user</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <form method="POST" action="{{ route('users.store') }}" class="ln-form-card space-y-3">
            @csrf
            <div class="ln-section-title">Tambah User Baru</div>
            <div>
                <label class="ln-label">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="ln-input" placeholder="Nama Karyawan">
            </div>
            <div>
                <label class="ln-label">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="ln-input" placeholder="user@example.com">
            </div>
            <div>
                <label class="ln-label">Password * (Minimal 6 Karakter)</label>
                <input type="text" name="password" value="{{ old('password') }}" required class="ln-input" placeholder="Ketikkan password">
                <p class="text-xs text-[var(--mute)] mt-1">Beri tahu password ini ke user agar mereka bisa login.</p>
            </div>
            <div>
                <label class="ln-label">Role *</label>
                <select name="role" class="ln-select" required>
                    <option value="staff" @selected(old('role') == 'staff')>Staff</option>
                    <option value="admin" @selected(old('role') == 'admin')>Admin</option>
                </select>
            </div>

            @if($isPro && $branches->count() > 0)
            <div class="pt-2 border-t border-[var(--hair)] mt-2">
                <label class="ln-label text-blue-600">Akses Cabang (Untuk Staff)</label>
                <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                    @foreach($branches as $b)
                        <label class="flex items-center gap-2 cursor-pointer p-2 hover:bg-[rgba(0,0,0,.03)] rounded-lg transition">
                            <input type="checkbox" name="branch_ids[]" value="{{ $b->id }}" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-[var(--ink)] font-medium">{{ $b->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            <button class="ln-btn-primary w-full justify-center mt-3">Simpan User</button>
        </form>
    </div>
</div>

@endsection