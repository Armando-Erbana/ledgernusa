@extends('layouts.app')
@section('title', 'Import Mutasi Bank')
@section('content')

<div class="ln-page-head">
    <div>
        <h1 class="ln-page-title">Import Mutasi Bank</h1>
        <p class="ln-page-sub">Upload file CSV mutasi dari internet banking</p>
    </div>
    <a href="{{ route('bank-rec.index') }}" class="ln-action">← Kembali</a>
</div>

<div class="ln-form-card max-w-2xl space-y-4">
    <div class="ln-alert ln-alert-success">
        <strong>Format CSV:</strong><br>
        <code style="font-size:11px">date,description,reference,debit,credit,balance</code><br>
        <code style="font-size:11px">2026-01-15,Transfer masuk,INV-001,1000000,,5000000</code><br>
        <code style="font-size:11px">2026-01-16,Pembayaran supplier,BILL-001,,500000,4500000</code>
        <div style="margin-top:6px;font-size:12px;">Kolom wajib: <b>date, description, debit/credit</b>. Nama kolom fleksibel (tanggal, deskripsi, masuk, keluar, dll).</div>
    </div>

    <form method="POST" action="{{ route('bank-rec.store') }}" enctype="multipart/form-data" class="space-y-3">
        @csrf

        <div>
            <label class="ln-label">Akun Bank <span style="color:#C0392B">*</span></label>
            <select name="account_id" required class="ln-select">
                <option value="">- Pilih Akun Bank -</option>
                @foreach($bankAccounts as $a)
                    <option value="{{ $a->id }}" @selected(old('account_id') == $a->id)>{{ $a->code }} - {{ $a->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="ln-label">File CSV <span style="color:#C0392B">*</span></label>
            <input type="file" name="file" accept=".csv,.txt" required class="ln-input">
        </div>

        <div class="ln-form-actions pt-3">
            <button class="ln-btn-primary">Import & Cocokkan</button>
            <a href="{{ route('bank-rec.index') }}" class="ln-btn-cancel">Batal</a>
        </div>
    </form>
</div>

@endsection