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

    // Auto-create trial subscription (14 hari, paket pro)
    $plan = \App\Models\Plan::where('code', 'pro')->first()
         ?? \App\Models\Plan::first();

    if ($plan) {
        \App\Models\Subscription::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'status' => 'trial',
            'billing_cycle' => 'monthly',
            'trial_ends_at' => now()->addDays(14),
            'starts_at' => now(),
            'ends_at' => now()->addDays(14),
        ]);
    }

    session(['company_id' => $company->id]);

    return redirect()->route('dashboard')->with('success', 'Company berhasil dibuat. Trial 14 hari dimulai!');
}

    public function switch($id)
    {
        $company = auth()->user()->companies()->where('companies.id', $id)->firstOrFail();
        session(['company_id' => $company->id]);
        return redirect()->route('dashboard');
    }
}