<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Subscriptions/Index', [
            'plans' => SubscriptionPlan::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'monthly_fee' => 'required|numeric',
            'setup_fee' => 'required|numeric',
            'training_fee' => 'required|numeric',
            'user_limit' => 'required|integer',
            'features' => 'nullable|array',
        ]);

        SubscriptionPlan::create($request->all());

        return back()->with('success', 'Plan created successfully.');
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $plan->update($request->all());
        return back()->with('success', 'Plan updated successfully.');
    }
}
