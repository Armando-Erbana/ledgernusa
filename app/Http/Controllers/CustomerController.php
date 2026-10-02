<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('name')->get();
        return view('customers.index', compact('customers'));
    }

    public function create() { return view('customers.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:30',
            'npwp' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_term_days' => 'nullable|integer|min:0',
        ]);
        $data['company_id'] = session('company_id');
        Customer::create($data);
        return redirect()->route('customers.index')->with('success', 'Customer ditambahkan.');
    }

    public function edit(Customer $customer) { return view('customers.edit', compact('customer')); }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:30',
            'npwp' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_term_days' => 'nullable|integer|min:0',
        ]);
        $customer->update($data);
        return redirect()->route('customers.index')->with('success', 'Customer diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer dihapus.');
    }
}