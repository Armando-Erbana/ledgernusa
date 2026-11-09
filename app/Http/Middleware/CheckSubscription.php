<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Subscription;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $company = $user->activeCompany();
        
        if (!$company) {
            return redirect()->route('companies.index');
        }

        // Cek apakah ada langganan aktif ATAU sedang dalam masa trial
        $activeSub = Subscription::where('company_id', $company->id)
            ->where(function($q) {
                $q->where(function($sub1) {
                    $sub1->where('status', 'active')->where('ends_at', '>=', now());
                })->orWhere(function($sub2) {
                    $sub2->where('trial_ends_at', '>=', now());
                });
            })
            ->first();

        if (!$activeSub) {
            // Mode Read-Only (Blokir jika bukan GET, atau GET tapi ke form create/edit)
            if ($request->isMethod('get')) {
                if ($request->routeIs('*.create') || $request->routeIs('*.edit')) {
                    return redirect()->route('subscription.index')
                        ->with('error', 'Masa trial habis. Silakan berlangganan untuk menambah data.');
                }
                return $next($request);
            }

            return redirect()->route('subscription.index')
                ->with('error', 'Masa trial habis. Silakan berlangganan untuk menyimpan data baru.');
        }

        return $next($request);
    }
}