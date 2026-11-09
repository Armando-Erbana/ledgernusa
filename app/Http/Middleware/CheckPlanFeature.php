<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Subscription;
use App\Models\Plan;

class CheckPlanFeature
{
    public function handle(Request $request, Closure $next, ...$allowedPlans): Response
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $companyId = session('company_id');
        
        // Ambil data langganan terbaru milik company ini
        $subscription = Subscription::where('company_id', $companyId)
            ->latest('id')
            ->first();

        $currentPlanCode = 'starter';

        if ($subscription) {
            // Cek apakah sedang trial
            $isTrial = $subscription->trial_ends_at && \Carbon\Carbon::parse($subscription->trial_ends_at)->isFuture();
            
            // Cek apakah langganan aktif (belum expired)
            $isActive = $subscription->status === 'active' && \Carbon\Carbon::parse($subscription->ends_at)->isFuture();

            if ($isTrial) {
                // Jika trial, anggap saja Pro
                $currentPlanCode = 'pro';
            } elseif ($isActive) {
                // Jika aktif bayar, ambil code dari tabel plans
                $plan = Plan::find($subscription->plan_id);
                if ($plan) {
                    $currentPlanCode = strtolower($plan->code);
                }
            }
        }

        $allowedPlans = array_map('strtolower', $allowedPlans);

        // Jika paket terbaca Pro/Enterprise, izinkan masuk
        if (in_array($currentPlanCode, $allowedPlans)) {
            return $next($request);
        }

        // Jika gagal, tendang ke halaman langganan dan tampilkan paket apa yang terbaca
        return redirect()->route('subscription.index')
            ->with('error', 'Fitur ini hanya tersedia untuk paket ' . ucfirst(implode(' atau ', $allowedPlans)) . '. Paket Anda saat ini: ' . strtoupper($currentPlanCode));
    }
}