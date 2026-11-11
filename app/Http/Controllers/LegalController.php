<?php

namespace App\Http\Controllers;

class LegalController extends Controller
{
    public function show($page)
    {
        $allowedPages = ['privacy', 'terms', 'dpa', 'surat-pernyataan'];
        abort_if(!in_array($page, $allowedPages), 404);

        return view("legal.{$page}");
    }
}