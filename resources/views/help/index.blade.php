@extends('layouts.app')
@section('title', 'Pusat Bantuan')

@section('content')
<div class="max-w-4xl mx-auto py-4">
    
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="ln-page-title">Pusat Bantuan</h1>
            <p class="ln-page-sub">Panduan lengkap penggunaan LedgerNusa</p>
        </div>
        <div class="text-sm text-[var(--mute)]">
            Butuh bantuan lain? <span class="font-bold text-[var(--b1)]">Tanya AI kami di pojok kanan bawah.</span>
        </div>
    </div>

    {{-- Styling Khusus untuk Markdown --}}
    <style>
        .manual-content h1 { font-size: 1.75rem; font-weight: 700; margin: 2rem 0 1rem; color: var(--ink); border-bottom: 2px solid var(--hair); padding-bottom: .5rem; }
        .manual-content h2 { font-size: 1.4rem; font-weight: 600; margin: 1.5rem 0 1rem; color: var(--ink); }
        .manual-content h3 { font-size: 1.15rem; font-weight: 600; margin: 1.25rem 0 .75rem; color: var(--soft); }
        .manual-content p { margin-bottom: 1rem; line-height: 1.7; color: var(--soft); }
        .manual-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; color: var(--soft); }
        .manual-content ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1rem; color: var(--soft); }
        .manual-content li { margin-bottom: 0.5rem; }
        .manual-content strong { color: var(--ink); font-weight: 700; }
        .manual-content blockquote { border-left: 4px solid var(--b1); padding: 1rem; background: rgba(37,99,235,.05); border-radius: 8px; color: var(--soft); font-size: 0.9rem; margin: 1rem 0; }
        .manual-content table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; font-size: 0.9rem; }
        .manual-content th, .manual-content td { border: 1px solid var(--hair); padding: 10px 14px; text-align: left; }
        .manual-content th { background: rgba(0,0,0,.03); font-weight: 600; color: var(--ink); }
        .manual-content hr { border: 0; border-top: 1px solid var(--hair); margin: 2rem 0; }
        .manual-content code { background: rgba(0,0,0,.05); padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 0.85rem; color: #B5362F; }
        .manual-content a { color: var(--b1); text-decoration: underline; }
    </style>

    <div class="ln-form-card manual-content">
        {!! $htmlContent !!}
    </div>
</div>
@endsection