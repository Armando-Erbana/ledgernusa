<?php
namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = auth()->user()->companies;
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'currency' => 'required|string|max:8',
            'address' => 'nullable|string',
        ]);

        $company = Company::create($data);
        auth()->user()->companies()->attach($company->id, ['role' => 'owner']);

        session(['company_id' => $company->id]);

        return redirect()->route('dashboard')->with('success', 'Company berhasil dibuat');
    }

    public function switch($id)
    {
        $company = auth()->user()->companies()->where('companies.id', $id)->firstOrFail();
        session(['company_id' => $company->id]);
        return redirect()->route('dashboard');
    }
}