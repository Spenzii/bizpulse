<?php

namespace App\Http\Controllers;

use App\Models\Wip;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WipController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'Active');
        $search = $request->query('search');
        $assignedToId = $request->query('assigned_to_id');
        $clientId = $request->query('client_id');

        $query = Wip::with(['client', 'assignedTo'])
            ->when($status !== 'All', function ($q) use ($status) {
                return $q->where('status', $status);
            })
            ->when($search, function ($q) use ($search) {
                return $q->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($cq) use ($search) {
                            $cq->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($assignedToId, function ($q) use ($assignedToId) {
                return $q->where('assigned_to_id', $assignedToId);
            })
            ->when($clientId, function ($q) use ($clientId) {
                return $q->where('client_id', $clientId);
            });

        return Inertia::render('Wips/Index', [
            'wips' => $query->latest()->get(),
            'filters' => [
                'status' => $status,
                'search' => $search,
                'assigned_to_id' => $assignedToId,
                'client_id' => $clientId,
            ],
            'stats' => [
                'Active' => Wip::where('status', 'Active')->count(),
                'Blocked' => Wip::where('status', 'Blocked')->count(),
                'Completed' => Wip::where('status', 'Completed')->count(),
                'All' => Wip::count(),
            ],
            'users' => User::where('role', '!=', 'super_admin')->get(['id', 'name']),
            'clients' => Client::all(['id', 'name']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Wips/Create', [
            'clients' => Client::all(),
            'users' => User::where('role', '!=', 'super_admin')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_fee' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'assigned_to_id' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'is_recurring' => 'boolean',
        ]);

        $wip = Wip::create($validated);

        if ($wip->assigned_to_id) {
            $wip->assignedTo->notify(new \App\Notifications\JobAssigned($wip));
        }

        return redirect()->route('wips.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Wip $wip)
    {
        $wip->load(['client', 'assignedTo', 'blockers.raisedBy', 'updates.user', 'auditLogs.user']);
        
        $resolvedBlockers = $wip->blockers->filter(fn($b) => $b->status === 'Resolved' && $b->resolved_at);
        $totalMinutes = 0;
        
        foreach ($resolvedBlockers as $blocker) {
            $start = \Carbon\Carbon::parse($blocker->created_at);
            $end = \Carbon\Carbon::parse($blocker->resolved_at);
            $totalMinutes += $start->diffInMinutes($end);
        }
        
        $count = $resolvedBlockers->count();
        $mttrMinutes = $count > 0 ? round($totalMinutes / $count) : null;
        
        // Format MTTR string
        $mttrString = null;
        if ($mttrMinutes !== null) {
            if ($mttrMinutes < 60) {
                $mttrString = $mttrMinutes . 'm';
            } elseif ($mttrMinutes < 1440) {
                $mttrString = round($mttrMinutes / 60, 1) . 'h';
            } else {
                $mttrString = round($mttrMinutes / 1440, 1) . 'd';
            }
        }

        return Inertia::render('Wips/Show', [
            'wip' => $wip,
            'metrics' => [
                'resolved_count' => $count,
                'mttr_minutes' => $mttrMinutes,
                'mttr_string' => $mttrString,
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wip $wip)
    {
        return Inertia::render('Wips/Edit', [
            'wip' => $wip,
            'clients' => Client::all(),
            'users' => User::where('role', '!=', 'super_admin')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wip $wip)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_fee' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'assigned_to_id' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'is_recurring' => 'boolean',
        ]);

        $wip->update($validated);

        return redirect()->route('wips.show', $wip->id)->with('success', 'Job updated successfully.');
    }

    /**
     * Bulk re-assign WIPs to a user.
     */
    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:wips,id',
            'assigned_to_id' => 'required|exists:users,id',
        ]);

        Wip::whereIn('id', $validated['ids'])->update([
            'assigned_to_id' => $validated['assigned_to_id']
        ]);

        return back()->with('success', 'Jobs successfully reassigned.');
    }

    /**
     * Duplicate WIPs for the next month (Roll over).
     */
    public function bulkDuplicate(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:wips,id',
        ]);

        $wips = Wip::whereIn('id', $validated['ids'])->get();
        $count = 0;

        foreach ($wips as $wip) {
            $newWip = $wip->replicate();
            
            // Set new due date to +1 month from current due date or today
            $currentDueDate = $wip->due_date ? \Carbon\Carbon::parse($wip->due_date) : now();
            $newWip->due_date = $currentDueDate->addMonth();
            
            // Reset status and progress
            $newWip->status = 'Active';
            $newWip->completed_at = null;
            $newWip->completion_note = null;
            $newWip->billing_status = 'Not Ready';
            
            $newWip->save();
            $count++;
        }

        return back()->with('success', "{$count} jobs rolled over to next month.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Wip::findOrFail($id)->delete();
        return redirect()->route('wips.index')->with('success', 'Job deleted successfully.');
    }
}
