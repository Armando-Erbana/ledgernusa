<?php
namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::where('company_id', session('company_id'))
            ->orderBy('name')->get();
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:customer,supplier,employee,other',
            'name' => 'required|string|max:255',
            'npwp' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        $data['company_id'] = session('company_id');
        Contact::create($data);
        return redirect()->route('contacts.index')->with('success', 'Kontak ditambahkan');
    }

    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $data = $request->validate([
            'type' => 'required|in:customer,supplier,employee,other',
            'name' => 'required|string|max:255',
            'npwp' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        $contact->update($data);
        return redirect()->route('contacts.index')->with('success', 'Kontak diperbarui');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Kontak dihapus');
    }
}