<?php

namespace App\Http\Controllers;

use App\Services\ChartOfAccountsSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnboardingController extends Controller
{
    public function index()
    {
        $company = auth()->user()->activeCompany();
        abort_unless($company, 403);

        if ($company->onboarding_completed_at) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.index', compact('company'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_type' => ['required', 'in:jasa,dagang'],
        ]);

        $company = auth()->user()->activeCompany();
        abort_unless($company, 403);

        DB::beginTransaction();
        try {
            $company->update([
                'business_type'           => $data['business_type'],
                'onboarding_completed_at' => now(),
            ]);

            app(ChartOfAccountsSeeder::class)->seed($company, $data['business_type']);

            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', 'Setup selesai! COA sudah disesuaikan untuk bisnis ' . ucfirst($data['business_type']) . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal menyimpan setup: ' . $e->getMessage());
        }
    }
}