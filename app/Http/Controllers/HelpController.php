<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

class HelpController extends Controller
{
    public function index()
    {
        // Baca file manual book dari storage
        $path = storage_path('app/manual/LedgerNusa_User_Manual.md');
        
        $content = file_exists($path) 
            ? file_get_contents($path) 
            : '# Manual Book Belum Tersedia. Silakan hubungi Administrator.';
        
        // Ubah format Markdown menjadi HTML
        $htmlContent = Str::markdown($content);

        return view('help.index', compact('htmlContent'));
    }
}