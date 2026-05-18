<?php

namespace App\Http\Controllers;

use App\Models\WeeklyReview;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class WeeklyReviewController extends Controller
{
    public function index()
    {
        return Inertia::render('WeeklyReviews/Index', [
            'reviews' => WeeklyReview::with('submittedBy')->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'summary' => 'required|string',
            'highlights' => 'nullable|array',
            'concerns' => 'nullable|array',
        ]);

        WeeklyReview::create([
            'week_number' => now()->weekOfYear,
            'year' => now()->year,
            'summary' => $request->summary,
            'highlights' => $request->highlights,
            'concerns' => $request->concerns,
            'submitted_by_id' => Auth::id(),
        ]);

        return redirect()->route('weekly-reviews.index')->with('success', 'Weekly review submitted.');
    }
}
