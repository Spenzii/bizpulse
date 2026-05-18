<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company_name' => 'required|string|max:255',
            'plan' => 'nullable|string|in:starter,business,professional,enterprise',
        ]);

        // Create the company
        $slugBase = Str::slug($request->company_name);
        $slug = $slugBase;
        $counter = 1;
        while (Company::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        $plan = $request->plan ? ucfirst($request->plan) : 'Starter';

        $company = Company::create([
            'name' => $request->company_name,
            'slug' => $slug,
            'primary_color' => '#0d9488', // Deep teal
            'secondary_color' => '#0f172a', // Dark navy
            'subscription_plan' => $plan,
            'subscription_status' => 'Trial',
            'trial_ends_at' => now()->addDays(14),
            'setup_fee_paid' => false,
            'onboarded' => false,
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'role' => 'company_admin',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('onboarding.index');
    }
}

