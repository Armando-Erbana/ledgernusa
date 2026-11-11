<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetActiveCompany
{
    public function handle(Request $request, Closure $next)
{
    if (! auth()->check()) {
        return $next($request);
    }

    if (auth()->user()->isSuperAdmin()) {
        return $next($request);
    }

    $user = auth()->user();
    $companyId = session('company_id');

    $validCompany = $companyId
        ? $user->companies()->where('companies.id', $companyId)->first()
        : null;

    if (! $validCompany) {
        $firstCompany = $user->companies()->first();

        if ($firstCompany) {
            session(['company_id' => $firstCompany->id]);
            $validCompany = $firstCompany;
        } else {
            if (! $request->routeIs('companies.*') && ! $request->routeIs('logout')) {
                return redirect()->route('companies.create')
                    ->with('info', 'Silakan buat company terlebih dahulu.');
            }
        }
    }

    // === ONBOARDING CHECK ===
    if ($validCompany
        && is_null($validCompany->onboarding_completed_at)
        && ! $request->routeIs('onboarding.*')
        && ! $request->routeIs('companies.*')
        && ! $request->routeIs('profile.*')
        && ! $request->routeIs('subscription.*') 
        && ! $request->routeIs('logout')) {
        return redirect()->route('onboarding.index');
    }

    return $next($request);
}
}