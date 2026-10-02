<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalePaymentController extends Controller
{
    public function create(Sale $sale)
    {
        $cashAccounts = Account::where(function ($q) {
                $q->where('is_cash', true)->orWhere('is_bank', true);
            })
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('sales.payment', compact('sale', 'cashAccounts'));
    }

    public function store(Request $request, Sale $sale)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'cash_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01|max:' . $sale->balance,
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $sale) {
            $payment = SalePayment::create([
                'company_id' => session('company_id'),
                'sale_id' => $sale->id,
                'date' => $data['date'],
                'cash_account_id' => $data['cash_account_id'],
                'amount' => $data['amount'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $journal = $payment->generateJournal();
            $payment->update(['journal_id' => $journal->id]);

            $newPaid = $sale->paid_amount + $data['amount'];
            $sale->update([
                'paid_amount' => $newPaid,
                'status' => $newPaid >= $sale->total ? 'paid' : 'partial',
            ]);
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Pembayaran diterima.');
    }
}