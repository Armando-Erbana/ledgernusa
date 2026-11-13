<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with(['user', 'company'])
            ->orderByRaw("FIELD(status, 'open') DESC")
            ->latest()
            ->paginate(15);

        return view('super-admin.support-tickets.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'company']);
        return view('super-admin.support-tickets.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'reply' => 'required|string'
        ]);

        $ticket->update([
            'reply' => $request->reply,
            'status' => 'replied'
        ]);

        return back()->with('success', 'Balasan berhasil dikirim ke user.');
    }
}