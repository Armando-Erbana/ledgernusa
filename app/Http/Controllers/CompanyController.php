<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\ChartOfAccountsSeeder;
use App\Services\CompanyAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = auth()->user()->companies()->orderBy('company_users.created_at')->get();
        $accessService = app(CompanyAccessService::class);
        $lockedIds = $accessService->lockedCompanyIds(auth()->user());
        $remainingSlots = $accessService->remainingSlots(auth()->user());

        return view('companies.index', compact('companies', 'lockedIds', 'remainingSlots'));
    }

    public function create()
    {
        $accessService = app(CompanyAccessService::class);
        $remaining = $accessService->remainingSlots(auth()->user());

        if ($remaining <= 0) {
            return redirect()->route('subscription.index')
                ->with('error', 'Kuota company untuk plan Anda sudah penuh. Upgrade untuk menambah.');
        }

        return view('companies.create', compact('remaining'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'currency'      => 'required|string|max:8',
            'business_type' => 'required|in:jasa,dagang',
            'address'       => 'nullable|string',
        ]);

        $user = auth()->user();
        $accessService = app(CompanyAccessService::class);

        if ($accessService->remainingSlots($user) <= 0) {
            return back()->withInput()
                ->with('error', 'Kuota company penuh. Upgrade plan untuk menambah.');
        }

        DB::beginTransaction();
        try {
            $company = Company::create([
                'name'                    => $data['name'],
                'currency'                => $data['currency'],
                'business_type'           => $data['business_type'],
                'onboarding_completed_at' => now(),  // langsung selesai, sudah pilih tipe
                'address'                 => $data['address'] ?? null,
            ]);

            $user->companies()->attach($company->id, ['role' => 'owner']);

            // Seed COA sesuai tipe
            app(ChartOfAccountsSeeder::class)->seed($company, $data['business_type']);

            // Auto-create trial subscription jika belum pernah
            $hasAnySub = Subscription::whereIn('company_id', $user->companies()->pluck('companies.id'))
                ->where('trial_ends_at', '>=', now())
                ->exists();

            if (! $hasAnySub) {
                $plan = Plan::where('code', 'pro')->first() ?? Plan::first();
                if ($plan) {
                    Subscription::create([
                        'company_id'    => $company->id,
                        'plan_id'       => $plan->id,
                        'status'        => 'trial',
                        'billing_cycle' => 'monthly',
                        'trial_ends_at' => now()->addDays(14),
                        'starts_at'     => now(),
                        'ends_at'       => now()->addDays(14),
                    ]);
                }
            }

            // Langsung switch ke company baru
            session(['company_id' => $company->id]);
            session()->forget('branch_id');

            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', "Company {$company->name} berhasil dibuat.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal membuat company: ' . $e->getMessage());
        }
    }

    public function switch($id)
    {
        $company = auth()->user()->companies()
            ->where('companies.id', $id)
            ->firstOrFail();

        $accessService = app(CompanyAccessService::class);
        if (! $accessService->canAccess(auth()->user(), $company->id)) {
            return redirect()->route('subscription.index')
                ->with('error', 'Company ini terkunci karena melebihi kuota plan Anda. Upgrade untuk mengakses.');
        }

        session(['company_id' => $company->id]);
        session()->forget('branch_id');  // reset branch agar tidak bentrok antar company

        return redirect()->route('dashboard');
    }

    public function updateBusinessType(Request $request, $id)
{
    $request->validate([
        'business_type' => 'required|in:jasa,dagang',
    ]);

    $company = auth()->user()->companies()->where('companies.id', $id)->firstOrFail();

    // Cek apakah company benar-benar kosong (belum ada data keuangan)
    $hasJournals = \App\Models\Journal::where('company_id', $company->id)->exists();
    $hasSales = \App\Models\Sale::where('company_id', $company->id)->exists();
    $hasPurchases = \App\Models\Purchase::where('company_id', $company->id)->exists();
    $hasCash = \App\Models\CashTransaction::where('company_id', $company->id)->exists();

    if ($hasJournals || $hasSales || $hasPurchases || $hasCash) {
        return back()->with('error', 'Gagal! Anda tidak dapat mengubah jenis bisnis karena sudah ada data transaksi/jurnal di perusahaan ini.');
    }

    if ($company->business_type === $request->business_type) {
        return back()->with('error', 'Jenis bisnis sudah sama.');
    }

    \Illuminate\Support\Facades\DB::transaction(function () use ($company, $request) {
        // 1. Hapus COA lama
        \App\Models\Account::where('company_id', $company->id)->delete();

        // 2. Update tipe bisnis
        $company->update(['business_type' => $request->business_type]);

        // 3. Buatkan COA baru sesuai tipe yang dipilih
        app(\App\Services\ChartOfAccountsSeeder::class)->seed($company, $request->business_type);
    });

    return back()->with('success', 'Berhasil! Jenis bisnis telah diubah dan COA baru telah dibuat.');
    }

}