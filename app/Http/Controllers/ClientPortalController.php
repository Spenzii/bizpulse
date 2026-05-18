<?php

namespace App\Http\Controllers;

use App\Models\Wip;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ClientPortalController extends Controller
{
    public function dashboard()
    {
        $client = Auth::user()->client;

        if (!$client) {
            abort(403, 'User is not linked to a client.');
        }

        $activeWips = Wip::where('client_id', $client->id)
            ->whereIn('status', ['Active', 'Blocked'])
            ->with(['assignedTo', 'blockers'])
            ->latest()
            ->get();

        $completedWips = Wip::where('client_id', $client->id)
            ->where('status', 'Completed')
            ->limit(5)
            ->latest('completed_at')
            ->get();

        $invoices = \App\Models\Invoice::where('client_id', $client->id)
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Client/Dashboard', [
            'client' => $client->load('company'),
            'active_wips' => $activeWips,
            'completed_wips' => $completedWips,
            'invoices' => $invoices,
        ]);
    }

    public function showWip(Wip $wip)
    {
        // Ensure the WIP belongs to the client
        if ($wip->client_id !== Auth::user()->client_id) {
            abort(403);
        }

        return Inertia::render('Client/WipShow', [
            'wip' => $wip->load(['blockers', 'updates' => function($q) {
                $q->latest();
            }]),
        ]);
    }
}
