<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SopController extends Controller
{
    /**
     * Display the official BizPulse Operating Rhythm SOP page.
     */
    public function index(): Response
    {
        return Inertia::render('Help/OperatingRhythm', [
            'rule_quote' => 'If it is not in BizPulse, it is not official.',
        ]);
    }
}
