@extends('layouts.app')
@section('title', 'COA')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Chart of Accounts</h1>
        <p class="ln-page-sub">Daftar akun dan saldo berjalan</p>
    </div>
    <a href="{{ route('accounts.create') }}" class="ln-btn-primary">+ Akun</a>
</div>

<div class="ln-table-card">
    <table class="ln-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Tipe</th>
                <th class="num">Saldo</th>
                <th class="center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $a)
                <tr>
                    <td class="ln-code">{{ $a->code }}</td>
                    <td>{{ $a->name }}</td>
                    <td><span class="ln-type {{ strtolower($a->type) }}">{{ $a->type }}</span></td>
                    <td class="num">Rp {{ number_format($a->balance, 0, ',', '.') }}</td>
                    <td class="center whitespace-nowrap">
                        <a href="{{ route('accounts.edit', $a) }}" class="ln-action">Edit</a>
                        <span class="ln-action-sep">·</span>
                        <form method="POST" action="{{ route('accounts.destroy', $a) }}" class="inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="ln-action red">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Belum ada akun</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection