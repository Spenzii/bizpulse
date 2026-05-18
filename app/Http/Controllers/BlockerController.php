<?php

namespace App\Http\Controllers;

use App\Models\Blocker;
use App\Models\Wip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockerController extends Controller
{
    public function store(Request $request, Wip $wip)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'priority' => 'required|string|in:Low,Medium,High,Urgent',
        ]);

        $blocker = $wip->blockers()->create([
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'raised_by_id' => Auth::id(),
            'status' => 'Open',
        ]);

        $wip->update(['status' => 'Blocked']);

        // Notify relevant users
        $usersToNotify = \App\Models\User::where('role', 'company_admin')
            ->orWhere('id', $wip->assigned_to_id)
            ->get();

        foreach ($usersToNotify as $user) {
            $user->notify(new \App\Notifications\BlockerRaised($wip, $blocker));
        }

        return back();
    }

    public function resolve(Blocker $blocker)
    {
        $blocker->update([
            'status' => 'Resolved',
            'resolved_at' => now(),
        ]);

        $wip = $blocker->wip;
        
        // If no more open blockers, set WIP back to Active
        if ($wip->blockers()->where('status', 'Open')->count() === 0) {
            $wip->update(['status' => 'Active']);
        }

        return back();
    }
}
