<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())->latest()->get();
        return view('support.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        SupportTicket::create([
            'company_id' => session('company_id'),
            'user_id' => auth()->id(),
            'subject' => $data['subject'],
            'message' => $data['message'],
        ]);

        return back()->with('success', 'Tiket bantuan berhasil dikirim. Tim kami akan segera membalas.');
    }
}