<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Wip;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        return Inertia::render('Invoices/Index', [
            'invoices' => Invoice::with('client')->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'wip_ids' => 'required|array',
            'wip_ids.*' => 'exists:wips,id',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $company = Auth::user()->company;
            
            // Generate invoice number (simple format: INV-YEAR-COUNT)
            $count = Invoice::count() + 1;
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $wips = Wip::whereIn('id', $validated['wip_ids'])->get();
            $subtotal = $wips->sum('estimated_fee');
            $tax = $subtotal * 0.10; // 10% GST
            $total = $subtotal + $tax;

            $invoice = Invoice::create([
                'company_id' => Auth::user()->company_id,
                'client_id' => $validated['client_id'],
                'invoice_number' => $invoiceNumber,
                'total_amount' => $total,
                'tax_amount' => $tax,
                'due_date' => $validated['due_date'],
                'status' => 'Draft',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($wips as $wip) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'wip_id' => $wip->id,
                    'description' => $wip->name,
                    'amount' => $wip->estimated_fee,
                ]);

                // Update WIP billing status
                $wip->update(['billing_status' => 'Invoiced']);
            }

            return redirect()->route('invoices.show', $invoice->id)->with('success', 'Invoice generated successfully.');
        });
    }

    public function show(Invoice $invoice)
    {
        $user = Auth::user();
        if ($user->role === 'client' && (int)$invoice->client_id !== (int)$user->client_id) {
            abort(403);
        }

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice->load(['client', 'items.wip', 'company']),
        ]);
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Draft,Sent,Paid,Cancelled',
        ]);

        $invoice->update(['status' => $validated['status']]);

        return back()->with('success', "Invoice status updated to {$validated['status']}.");
    }

    public function send(Invoice $invoice)
    {
        $user = Auth::user();
        
        // Ensure the user belongs to the same company
        if ((int)$invoice->company_id !== (int)$user->company_id) {
            abort(403);
        }

        // Send email
        \Illuminate\Support\Facades\Mail::to($invoice->client->email)
            ->send(new \App\Mail\InvoiceMail($invoice));

        // Update status
        $invoice->update(['status' => 'Sent']);

        return back()->with('success', "Invoice {$invoice->invoice_number} sent to client.");
    }
}
