<?php

namespace App\Http\Controllers;

use App\Models\Wip;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyBillingController extends Controller
{
    public function index()
    {
        return Inertia::render('Billing/Index', [
            'billable_wips' => Wip::with('client')
                ->whereIn('billing_status', ['Ready to Invoice', 'Invoiced', 'Paid'])
                ->latest()
                ->get(),
        ]);
    }

    public function updateStatus(Request $request, Wip $wip)
    {
        $request->validate([
            'billing_status' => 'required|string',
        ]);

        $wip->update(['billing_status' => $request->billing_status]);

        return back()->with('success', 'Billing status updated.');
    }
}
