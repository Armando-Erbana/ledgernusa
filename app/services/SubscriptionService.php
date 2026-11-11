<?php

namespace App\Services;

use App\Models\Company;
use App\Models\PaymentProof;
use App\Models\Subscription;

class SubscriptionService
{
    public function renewFromProof(PaymentProof $proof): ?Subscription
    {
        $company = Company::find($proof->company_id);
        $plan = $proof->plan;

        if (!$company || !$plan) {
            return null;
        }

        $duration = $plan->duration_days ?? 30;
        $existing = $company->subscription;

        // Jika user sudah berstatus 'active' (memperpanjang langganan)
        if ($existing && $existing->status === 'active' && $existing->ends_at && $existing->ends_at->isFuture()) {
            $existing->update([
                'ends_at' => $existing->ends_at->copy()->addDays($duration), // Tambah dari sisa hari aktif
                'plan_id' => $plan->id,
            ]);
            return $existing;
        }

        // Jika user masih TRIAL atau EXPIRED, timpa dengan 30 hari penuh mulai hari ini
        return Subscription::updateOrCreate(
            ['company_id' => $company->id],
            [
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($duration), // Pas 30 hari
                'status' => 'active',
                'trial_ends_at' => null, // Matikan trial
            ]
        );
    }
}