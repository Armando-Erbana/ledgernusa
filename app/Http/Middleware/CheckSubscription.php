<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $companyId = session('company_id');
        if (!$companyId) {
            return $next($request);
        }

        $company = Company::find($companyId);
        $sub = $company?->subscription;

        // Belum ada subscription = boleh
        if (!$sub) {
            return $next($request);
        }

        // Aktif atau trial = lanjut
        if ($sub->isActive()) {
            if ($sub->status === 'trial') {
                session()->flash('trial_days', $sub->daysRemaining());
            }
            return $next($request);
        }

        // Expired: hanya boleh akses subscription & logout
        if ($request->routeIs('subscription.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        return redirect()->route('subscription.expired');
    }
}