<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Journal;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = session('company_id');

        $totalKas = 0;
        $totalBank = 0;
        if ($companyId) {
            $totalKas = Account::where('company_id', $companyId)
                ->where('is_cash', true)->get()->sum('balance');
            $totalBank = Account::where('company_id', $companyId)
                ->where('is_bank', true)->get()->sum('balance');
        }

        $recentJournals = $companyId
            ? Journal::where('company_id', $companyId)->latest()->limit(5)->get()
            : collect();

        return view('dashboard', compact('totalKas', 'totalBank', 'recentJournals'));
    }
}