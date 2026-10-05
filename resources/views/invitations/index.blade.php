@extends('layouts.app')
@section('title', 'Undang User')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Undang User</h1>
        <p class="ln-page-sub">Kirim undangan bergabung ke company Anda</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="md:col-span-2">
        <h2 class="ln-section-title">Riwayat Undangan</h2>
        <div class="ln-table-card">
            <table class="ln-table">
                <thead>
                    <tr><th>Email</th><th>Role</th><th>Status</th><th>Dikirim</th><th>Kedaluwarsa</th><th class="center">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse($invitations as $inv)
                    <tr>
                        <td>{{ $inv->email }}</td>
                        <td><span class="ln-badge posted">{{ $inv->role }}</span></td>
                        <td>
                            @if($inv->accepted_at)
                                <span class="ln-badge posted">Diterima</span>
                            @elseif($inv->isExpired())
                                <span class="ln-badge unpaid">Kedaluwarsa</span>
                            @else
                                <span class="ln-badge draft">Menunggu</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap">{{ $inv->created_at->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap">{{ $inv->expires_at->format('d/m/Y H:i') }}</td>
                        <td class="center">
                            @if($inv->isValid())
                                <form method="POST" action="{{ route('invitations.destroy', $inv) }}" class="inline" onsubmit="return confirm('Batalkan undangan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="ln-action red">Batalkan</button>
                                </form>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada undangan</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="ln-pagination">{{ $invitations->links() }}</div>
    </div>

    <div>
        <form method="POST" action="{{ route('invitations.store') }}" class="ln-form-card space-y-3">
            @csrf
            <div class="ln-section-title">Kirim Undangan Baru</div>
            <div>
                <label class="ln-label">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="ln-input" placeholder="user@example.com">
            </div>
            <div>
                <label class="ln-label">Role *</label>
                <select name="role" class="ln-select" required>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="text-xs text-gray-500">
                Link undangan berlaku <strong>7 hari</strong>. Penerima akan menerima email berisi tombol untuk bergabung.
            </div>
            <button class="ln-btn-primary w-full justify-center">Kirim Undangan</button>
        </form>
    </div>
</div>

@endsection