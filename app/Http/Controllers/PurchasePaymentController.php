<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchasePaymentController extends Controller
{
    public function create(Purchase $purchase)
    {
        $cashAccounts = Account::where(fn($q) => $q->where('is_cash', true)->orWhere('is_bank', true))
            ->where('is_active', true)->orderBy('code')->get();
        return view('purchases.payment', compact('purchase', 'cashAccounts'));
    }

    public function store(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'cash_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01|max:' . $purchase->balance,
            'pph23' => 'nullable|numeric|min:0|max:' . $purchase->balance,
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $purchase) {
            $payment = PurchasePayment::create([
                'company_id' => session('company_id'),
                'purchase_id' => $purchase->id,
                'date' => $data['date'],
                'cash_account_id' => $data['cash_account_id'],
                'amount' => $data['amount'],
                'pph23' => $data['pph23'] ?? 0,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $journal = $payment->generateJournal();
            $payment->update(['journal_id' => $journal->id]);

            $paid = $purchase->paid_amount + $data['amount'] + ($data['pph23'] ?? 0);
            $purchase->update([
                'paid_amount' => $paid,
                'status' => $paid >= $purchase->total ? 'paid' : 'partial',
            ]);
        });

        return redirect()->route('purchases.show', $purchase)->with('success', 'Pembayaran dicatat.');
    }
}