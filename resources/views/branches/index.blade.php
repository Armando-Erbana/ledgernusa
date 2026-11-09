@extends('layouts.app')
@section('title', 'Manajemen Cabang')

@section('content')
<div class="max-w-5xl mx-auto py-4">
    
    <div class="mb-6">
        <h1 class="ln-page-title">Manajemen Cabang</h1>
        <p class="ln-page-sub">Kelola daftar cabang untuk perusahaan Anda.</p>
    </div>

    @if(session('success'))
        <div class="ln-alert ln-alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="ln-alert ln-alert-error mb-4">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Tabel Daftar Cabang --}}
        <div class="md:col-span-2">
            <div class="ln-table-card">
                <table class="ln-table">
                    <thead>
                        <tr>
                            <th>Nama Cabang</th>
                            <th>Kode</th>
                            <th>Status</th>
                            <th class="center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                            <tr>
                                <td>
                                    <div class="font-bold">{{ $branch->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $branch->address ?: '-' }}</div>
                                </td>
                                <td><span class="ln-code">{{ $branch->code ?: '-' }}</span></td>
                                <td>
                                    @if($branch->is_main)
                                        <span class="ln-badge posted">Utama</span>
                                    @else
                                        <span class="ln-badge draft">Cabang</span>
                                    @endif
                                </td>
                                <td class="center">
                                    @if(session('branch_id') == $branch->id)
                                        <span class="text-xs text-blue-600 font-bold px-2 py-1 bg-blue-50 rounded-lg">Aktif</span>
                                    @else
                                        <a href="{{ route('branches.switch', $branch) }}" class="ln-action">Gunakan</a>
                                        @if($branches->count() > 1)
                                            <span class="ln-action-sep">|</span>
                                            <form method="POST" action="{{ route('branches.destroy', $branch) }}" class="inline" onsubmit="return confirm('Hapus cabang ini?')">
                                                @csrf @method('DELETE')
                                                <button class="ln-action red">Hapus</button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">Belum ada cabang.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Form Tambah Cabang --}}
        <div>
            <form method="POST" action="{{ route('branches.store') }}" class="ln-form-card space-y-4">
                @csrf
                <div class="ln-section-title">Tambah Cabang Baru</div>
                
                <div>
                    <label class="ln-label">Nama Cabang *</label>
                    <input type="text" name="name" required class="ln-input" placeholder="Contoh: Cabang Bandung">
                </div>
                
                <div>
                    <label class="ln-label">Kode</label>
                    <input type="text" name="code" class="ln-input" placeholder="BDG-01">
                </div>
                
                <div>
                    <label class="ln-label">Alamat</label>
                    <textarea name="address" rows="2" class="ln-textarea" placeholder="Alamat lengkap..."></textarea>
                </div>
                
                <label class="ln-checkbox">
                    <input type="checkbox" name="is_main" value="1"> 
                    Jadikan Cabang Utama
                </label>
                
                <button class="ln-btn-primary w-full justify-center">Simpan Cabang</button>
            </form>
        </div>
    </div>
</div>
@endsection