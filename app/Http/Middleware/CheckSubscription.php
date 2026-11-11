<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use App\Services\TrialLimitService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $company = $user->activeCompany();

        if (! $company) {
            return redirect()->route('companies.index');
        }

        // === CEK LIMIT TRIAL (1.000 jurnal) ===
        $trial = app(TrialLimitService::class);
        if ($trial->shouldBeReadOnly($user)) {
            return $this->readOnly($request, $next,
                'Batas 1.000 jurnal selama trial sudah tercapai. Silakan berlangganan untuk melanjutkan.');
        }

        // === CEK SUBSCRIPTION AKTIF ===
        $activeSub = Subscription::where('company_id', $company->id)
            ->where(function ($q) {
                $q->where(fn ($s) => $s->where('status', 'active')->where('ends_at', '>=', now()))
                  ->orWhere(fn ($s) => $s->where('trial_ends_at', '>=', now()));
            })
            ->first();

        if (! $activeSub) {
            return $this->readOnly($request, $next,
                'Masa aktif habis. Silakan berlangganan untuk menyimpan data baru.');
        }

        return $next($request);
    }

    private function readOnly(Request $request, Closure $next, string $message): Response
    {
        // GET diizinkan (read-only), kecuali ke form create/edit
        if ($request->isMethod('get')) {
            if ($request->routeIs('*.create') || $request->routeIs('*.edit')) {
                return redirect()->route('subscription.index')->with('error', $message);
            }
            return $next($request);
        }

        // POST/PUT/PATCH/DELETE diblokir
        return redirect()->route('subscription.index')->with('error', $message);
    }
}