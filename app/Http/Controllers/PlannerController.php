<?php

namespace App\Http\Controllers;

use App\Models\Wip;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlannerController extends Controller
{
    public function index(Request $request)
    {
        $assignedToId = $request->query('assigned_to_id');

        $query = Wip::with(['client', 'assignedTo'])
            ->when($assignedToId, function ($q) use ($assignedToId) {
                return $q->where('assigned_to_id', $assignedToId);
            });

        return Inertia::render('Planner/Index', [
            'wips' => $query->get(),
            'users' => User::where('role', '!=', 'super_admin')->get(['id', 'name']),
            'filters' => [
                'assigned_to_id' => $assignedToId,
            ],
        ]);
    }
}
