<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetActiveCompany
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        // Super admin tidak butuh company
        if (auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        $user = auth()->user();
        $companyId = session('company_id');

        // Validasi: company_id harus milik user
        $validCompany = null;
        if ($companyId) {
            $validCompany = $user->companies()
                ->where('companies.id', $companyId)
                ->first();
        }

        // Kalau invalid atau belum di-set, ambil company pertama
        if (!$validCompany) {
            $firstCompany = $user->companies()->first();

            if ($firstCompany) {
                session(['company_id' => $firstCompany->id]);
            } else {
                // User belum punya company — redirect ke halaman buat company
                if (!$request->routeIs('companies.*') && !$request->routeIs('logout')) {
                    return redirect()->route('companies.create')
                        ->with('info', 'Silakan buat company terlebih dahulu.');
                }
            }
        }

        return $next($request);
    }
}