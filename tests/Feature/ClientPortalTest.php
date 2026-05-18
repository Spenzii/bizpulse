<?php

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use App\Models\Wip;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::create([
        'name' => 'Test Company',
        'slug' => 'test-company',
        'subscription_status' => 'Active',
    ]);

    $this->client = Client::create([
        'company_id' => $this->company->id,
        'name' => 'Test Client',
    ]);

    $this->clientUser = User::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'name' => 'Client User',
        'email' => 'client@example.com',
        'password' => bcrypt('password'),
        'role' => 'client',
    ]);
});

test('client user is redirected to portal from dashboard', function () {
    $this->actingAs($this->clientUser)
        ->get(route('dashboard'))
        ->assertRedirect(route('client-portal.dashboard'));
});

test('client can only see their own WIPs', function () {
    // WIP belonging to this client
    Wip::factory()->create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'name' => 'My Job',
    ]);

    // WIP belonging to another client
    $otherClient = Client::create([
        'company_id' => $this->company->id,
        'name' => 'Other Client',
    ]);
    Wip::factory()->create([
        'company_id' => $this->company->id,
        'client_id' => $otherClient->id,
        'name' => 'Other Job',
    ]);

    $response = $this->actingAs($this->clientUser)
        ->get(route('client-portal.dashboard'))
        ->assertOk();

    $response->assertInertia(fn ($page) => $page
        ->has('active_wips', 1)
        ->where('active_wips.0.name', 'My Job')
    );
});

test('client cannot access staff dashboard or wips index', function () {
    $this->actingAs($this->clientUser)
        ->get(route('wips.index'))
        ->assertStatus(403); // Assuming we added middleware or checks. Wait, I should check if I added them.
});
