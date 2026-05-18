<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index()
    {
        return Inertia::render('Clients/Index', [
            'clients' => Client::withCount('wips')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Clients/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client added to register.');
    }

    public function show(Client $client)
    {
        return Inertia::render('Clients/Show', [
            'client' => $client->load('wips'),
            'portal_users' => User::where('client_id', $client->id)->get(),
        ]);
    }

    public function createPortalUser(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $password = Str::random(12);

        $user = User::create([
            'company_id' => $client->company_id,
            'client_id' => $client->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role' => 'client',
        ]);

        // In a real app, we would send an email with the password here
        return back()->with('success', "Portal user created for {$client->name}. Temporary Password: {$password}");
    }
}
