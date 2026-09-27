<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function create()
{
    return view('companies.create');
}

public function store(Request $request)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'currency' => 'required|string|max:8',
    ]);

    $company = Company::create($data);
    auth()->user()->companies()->attach($company->id, ['role' => 'owner']);

    return redirect()->route('dashboard');
}
}
