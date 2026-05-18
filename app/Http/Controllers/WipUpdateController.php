<?php

namespace App\Http\Controllers;

use App\Models\Wip;
use App\Models\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WipUpdateController extends Controller
{
    public function store(Request $request, Wip $wip)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'percentage_completed' => 'required|integer|min:0|max:100',
        ]);

        $wip->updates()->create([
            'content' => $validated['content'],
            'percentage_completed' => $validated['percentage_completed'],
            'user_id' => Auth::id(),
            'company_id' => $wip->company_id,
        ]);

        if ($validated['percentage_completed'] == 100) {
            $wip->update(['status' => 'Completed']);
        }

        return back();
    }
}
