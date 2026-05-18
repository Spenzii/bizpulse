<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Support/Tickets', [
            'tickets' => SupportTicket::with('company', 'user')->latest()->get(),
        ]);
    }

    public function leads()
    {
        return Inertia::render('Admin/Support/Leads', [
            'leads' => Lead::where('source', 'Contact')->latest()->get(),
        ]);
    }

    public function demoRequests()
    {
        return Inertia::render('Admin/Support/DemoRequests', [
            'leads' => Lead::where('source', 'Demo')->latest()->get(),
        ]);
    }

    public function update(Request $request, SupportTicket $ticket)
    {
        $ticket->update($request->only('status', 'priority'));
        return back()->with('success', 'Ticket updated.');
    }
}
