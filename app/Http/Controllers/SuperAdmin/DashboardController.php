<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\Subscription;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCompanies = Company::count();
        $totalUsers = User::where('is_super_admin', false)->count();
        $totalJournals = Journal::count();

        $trialCount = Subscription::where('status', 'trial')->count();
        $activeCount = Subscription::where('status', 'active')->count();
        $expiredCount = Subscription::whereIn('status', ['expired', 'cancelled'])->count();

        // Estimasi MRR dari subscription aktif
        $mrr = Subscription::where('status', 'active')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price_monthly');

        // Recent signups (company terbaru)
        $recentCompanies = Company::with(['users', 'subscription.plan'])
            ->latest()
            ->limit(10)
            ->get();

        // Subscription yang akan expired 7 hari ke depan
        $expiringSoon = Subscription::with(['company', 'plan'])
            ->whereIn('status', ['trial', 'active'])
            ->whereBetween('ends_at', [now(), now()->addDays(7)])
            ->orderBy('ends_at')
            ->get();

        return view('super-admin.dashboard', compact(
            'totalCompanies', 'totalUsers', 'totalJournals',
            'trialCount', 'activeCount', 'expiredCount',
            'mrr', 'recentCompanies', 'expiringSoon'
        ));
    }
}