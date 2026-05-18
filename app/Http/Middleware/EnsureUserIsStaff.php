<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info("Middleware checking user: " . (Auth::check() ? Auth::user()->email : 'Guest'));
        if (Auth::check() && Auth::user()->role === 'client') {
            abort(403, 'Clients cannot access internal staff tools.');
        }

        return $next($request);
    }
}
