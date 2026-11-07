<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentProofController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $query = PaymentProof::with(['company', 'plan', 'user'])->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $proofs = $query->paginate(20)->withQueryString();

        $counts = [
            'pending' => PaymentProof::where('status', 'pending')->count(),
            'approved' => PaymentProof::where('status', 'approved')->count(),
            'rejected' => PaymentProof::where('status', 'rejected')->count(),
        ];

        return view('super-admin.payment-proofs.index', compact('proofs', 'status', 'counts'));
    }

    public function show(PaymentProof $proof)
    {
        $proof->load(['company', 'plan', 'user', 'verifier', 'subscription']);
        return view('super-admin.payment-proofs.show', compact('proof'));
    }

    public function approve(PaymentProof $proof, SubscriptionService $subscriptionService)
    {
        abort_unless($proof->isPending(), 400, 'Bukti sudah diverifikasi.');

        DB::transaction(function () use ($proof, $subscriptionService) {
            $subscription = $subscriptionService->renewFromProof($proof);

            $proof->update([
                'status' => 'approved',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'subscription_id' => $subscription?->id,
            ]);
        });

        return redirect()
            ->route('super-admin.payment-proofs.show', $proof)
            ->with('success', 'Pembayaran disetujui. Langganan perusahaan diperpanjang.');
    }

    public function reject(Request $request, PaymentProof $proof)
    {
        abort_unless($proof->isPending(), 400, 'Bukti sudah diverifikasi.');

        $data = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $proof->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return redirect()
            ->route('super-admin.payment-proofs.show', $proof)
            ->with('success', 'Bukti ditolak.');
    }

    public function download(PaymentProof $proof)
    {
        abort_unless($proof->proof_file && Storage::disk('public')->exists($proof->proof_file), 404);

        return Storage::disk('public')->download(
            $proof->proof_file,
            'bukti-' . $proof->id . '-' . ($proof->company->name ?? 'company') . '.' . pathinfo($proof->proof_file, PATHINFO_EXTENSION)
        );
    }
}