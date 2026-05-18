<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Payments/Index', [
            'payments' => Payment::with('company')->latest()->get(),
            'companies' => Company::all(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'type' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        Payment::create($request->all());

        return back()->with('success', 'Payment recorded successfully.');
    }
}
