<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::with(['users', 'subscription.plan']);

        // Filter by status subscription
        if ($request->filled('status')) {
            $status = $request->get('status');
            $query->whereHas('subscription', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        // Search by name
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->get('q') . '%');
        }

        $tenants = $query->latest()->paginate(20);

        return view('super-admin.tenants.index', compact('tenants'));
    }

    public function show(Company $tenant)
    {
        $tenant->load(['users', 'subscriptions.plan', 'invoices']);
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('super-admin.tenants.show', compact('tenant', 'plans'));
    }

    public function activate(Request $request, Company $tenant)
    {
        $data = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'duration_days' => 'required|integer|min:1|max:3650',
        ]);

        // Update atau buat subscription
        $sub = $tenant->subscription;

        if ($sub) {
            $sub->update([
                'plan_id' => $data['plan_id'],
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addDays($data['duration_days']),
                'trial_ends_at' => null,
                'cancelled_at' => null,
            ]);
        } else {
            Subscription::create([
                'company_id' => $tenant->id,
                'plan_id' => $data['plan_id'],
                'status' => 'active',
                'billing_cycle' => 'monthly',
                'starts_at' => now(),
                'ends_at' => now()->addDays($data['duration_days']),
            ]);
        }

        return back()->with('success', "Langganan {$tenant->name} berhasil diaktifkan.");
    }

    public function extend(Request $request, Company $tenant)
    {
        $data = $request->validate([
            'days' => 'required|integer|min:1|max:3650',
        ]);

        $sub = $tenant->subscription;
        if (!$sub) {
            return back()->with('error', 'Tenant belum memiliki subscription.');
        }

        $baseDate = $sub->ends_at && $sub->ends_at->isFuture() ? $sub->ends_at : now();
        $sub->update([
            'ends_at' => $baseDate->copy()->addDays($data['days']),
            'status' => 'active',
        ]);

        return back()->with('success', "Langganan diperpanjang {$data['days']} hari.");
    }

    public function changePlan(Request $request, Company $tenant)
    {
        $data = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $sub = $tenant->subscription;
        if (!$sub) {
            return back()->with('error', 'Tenant belum memiliki subscription.');
        }

        $sub->update(['plan_id' => $data['plan_id']]);

        return back()->with('success', 'Paket berhasil diubah.');
    }

    public function suspend(Company $tenant)
    {
        $sub = $tenant->subscription;
        if ($sub) {
            $sub->update([
                'status' => 'expired',
                'ends_at' => now(),
            ]);
        }

        return back()->with('success', "Langganan {$tenant->name} di-suspend.");
    }

    public function reactivate(Company $tenant)
    {
        $sub = $tenant->subscription;
        if (!$sub) {
            return back()->with('error', 'Tenant belum memiliki subscription.');
        }

        $sub->update([
            'status' => 'active',
            'ends_at' => now()->addDays(30),
        ]);

        return back()->with('success', "Langganan {$tenant->name} diaktifkan kembali.");
    }
}