<?php
namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Invoice;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        $company = \App\Models\Company::find(session('company_id'));
        $subscription = $company?->subscription;
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $invoices = Invoice::where('company_id', $company?->id)->latest()->get();

        return view('subscription.index', compact('company', 'subscription', 'plans', 'invoices'));
    }

    public function expired()
    {
        $company = \App\Models\Company::find(session('company_id'));
        $subscription = $company?->subscription;
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('subscription.expired', compact('company', 'subscription', 'plans'));
    }
}