<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\PaymentProof;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubscriptionController extends Controller
{
    public function index()
    {
        $company = Company::find(session('company_id'));
        $subscription = $company?->subscription;
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $invoices = Invoice::where('company_id', $company?->id)->latest()->take(10)->get();

        $proofs = PaymentProof::where('company_id', $company?->id)
            ->with('plan')
            ->latest()
            ->take(10)
            ->get();

        $selectedPlanId = request('plan');

        return view('subscription.index', compact(
            'company', 'subscription', 'plans', 'invoices', 'proofs', 'selectedPlanId'
        ));
    }

    public function expired()
    {
        $company = Company::find(session('company_id'));
        $subscription = $company?->subscription;
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('subscription.expired', compact('company', 'subscription', 'plans'));
    }

    /**
     * User upload bukti transfer manual.
     */
    public function uploadProof(Request $request)
    {
        $companyId = session('company_id');
        $company = Company::findOrFail($companyId);

        $data = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'amount' => 'required|numeric|min:1',
            'transfer_date' => 'required|date|before_or_equal:today',
            'bank_from' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
            'proof_file' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        $plan = Plan::findOrFail($data['plan_id']);

        // Nominal harus >= harga paket
        if ($data['amount'] < $plan->price) {
            return back()
                ->withInput()
                ->withErrors([
                    'amount' => 'Nominal transfer (Rp ' . number_format($data['amount'], 0, ',', '.') .
                                ') kurang dari harga paket (Rp ' . number_format($plan->price, 0, ',', '.') . ')'
                ]);
        }

        // Simpan file
        $file = $request->file('proof_file');
        $filename = 'proof-' . $company->id . '-' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('payment-proofs', $filename, 'public');

        // Buat record
        PaymentProof::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'user_id' => auth()->id(),
            'amount' => $data['amount'],
            'transfer_date' => $data['transfer_date'],
            'bank_from' => $data['bank_from'],
            'account_name' => $data['account_name'],
            'proof_file' => $path,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('subscription.index')
            ->with('success', 'Bukti transfer berhasil diupload. Menunggu verifikasi admin (1x24 jam).');
    }

    /**
     * User hapus bukti yang masih pending.
     */
    public function deleteProof(PaymentProof $proof)
    {
        abort_unless($proof->company_id === session('company_id'), 403);
        abort_unless($proof->isPending(), 403, 'Hanya bukti pending yang bisa dihapus.');

        if ($proof->proof_file && Storage::disk('public')->exists($proof->proof_file)) {
            Storage::disk('public')->delete($proof->proof_file);
        }
        $proof->delete();

        return back()->with('success', 'Bukti transfer dihapus.');
    }
}