<?php

namespace App\Services;

use App\Models\Company;
use App\Models\PaymentProof;
use App\Models\Subscription;

class SubscriptionService
{
    /**
     * Perpanjang / buat subscription baru setelah bukti transfer di-approve.
     */
    public function renewFromProof(PaymentProof $proof): ?Subscription
    {
        $company = Company::find($proof->company_id);
        $plan = $proof->plan;

        if (!$company || !$plan) {
            return null;
        }

        $duration = $plan->duration_days ?? 30;
        $existing = $company->subscription;

        if ($existing && $existing->ends_at && $existing->ends_at->isFuture()) {
            // Masih aktif → tambah durasi
            $existing->update([
                'ends_at' => $existing->ends_at->copy()->addDays($duration),
                'status' => 'active',
                'plan_id' => $plan->id,
            ]);
            return $existing;
        }

        // Expired atau belum ada → buat baru
        return Subscription::updateOrCreate(
            ['company_id' => $company->id],
            [
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => now()->addDays($duration),
                'status' => 'active',
            ]
        );
    }
}