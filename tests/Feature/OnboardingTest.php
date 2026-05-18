<?php

use App\Models\Company;
use App\Models\User;
use App\Models\Client;
use App\Models\Wip;
use App\Models\ProgressUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::create([
        'name' => 'Spenzii Corp',
        'slug' => 'spenzii-corp',
        'onboarded' => false,
    ]);

    $this->admin = User::create([
        'company_id' => $this->company->id,
        'name' => 'Stanley Mark',
        'email' => 'stanley@example.com',
        'password' => bcrypt('password'),
        'role' => 'company_admin',
    ]);
});

test('guest registration automatically creates a company and assigns company_admin role', function () {
    $response = $this->post(route('register'), [
        'name' => ' Felix Gabriel',
        'email' => 'felix@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'company_name' => 'SmartBiz Mt Hagen',
        'plan' => 'business',
    ]);

    $response->assertRedirect(route('onboarding.index'));

    $user = User::where('email', 'felix@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('company_admin');
    
    $company = $user->company;
    expect($company)->not->toBeNull();
    expect($company->name)->toBe('SmartBiz Mt Hagen');
    expect($company->subscription_plan)->toBe('Business');
    expect($company->onboarded)->toBeFalse();
});

test('non-onboarded staff are forced to redirect to onboarding page', function () {
    // Attempting to visit dashboard redirects to /onboarding
    $response = $this->actingAs($this->admin)
        ->get(route('dashboard'));

    $response->assertRedirect(route('onboarding.index'));
});

test('onboarded staff can browse dashboard normally', function () {
    $this->company->update(['onboarded' => true]);

    $response = $this->actingAs($this->admin)
        ->get(route('dashboard'));

    $response->assertOk();
});

test('clients are not subject to onboarding middleware redirects', function () {
    $client = Client::create([
        'company_id' => $this->company->id,
        'name' => 'Mock Client Company',
        'status' => 'Active',
    ]);

    $clientUser = User::create([
        'company_id' => $this->company->id,
        'client_id' => $client->id,
        'name' => 'External Client',
        'email' => 'client@external.com',
        'password' => bcrypt('password'),
        'role' => 'client',
    ]);

    $response = $this->actingAs($clientUser)
        ->get(route('client-portal.dashboard'));

    $response->assertOk();
});

test('onboarding wizard successfully processes payload, seeds template tasks, invites staff, logs client and WIP', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('onboarding.store'), [
            'country' => 'Papua New Guinea',
            'city' => 'Port Moresby',
            'address_line_1' => 'Section 45 Lot 12, Waigani',
            'tax_id' => 'TIN-987654',
            'industry_type' => 'Accounting Firm',
            'staff' => [
                ['name' => 'Donald Wari', 'email' => 'donald@smartbiz.com'],
                ['name' => 'Leo Kari', 'email' => 'leo@smartbiz.com'],
            ],
            'client_name' => 'Ok Tedi Mining',
            'client_sector' => 'Mining',
            'wip_name' => 'Quarterly Audit Review Q2',
            'wip_priority' => 'high',
            'wip_estimated_fee' => 7500.00,
            'wip_due_date' => now()->addDays(10)->format('Y-m-d'),
            'wip_next_action' => 'Gather draft financial ledgers',
        ]);

    $response->assertRedirect(route('dashboard'));

    // Check company updates
    $this->company->refresh();
    expect($this->company->onboarded)->toBeTrue();
    expect($this->company->industry_type)->toBe('Accounting Firm');
    expect($this->company->address_line_1)->toBe('Section 45 Lot 12, Waigani');
    expect($this->company->tax_id)->toBe('TIN-987654');

    // Check staff accounts invited
    $donald = User::where('email', 'donald@smartbiz.com')->first();
    expect($donald)->not->toBeNull();
    expect($donald->company_id)->toBe($this->company->id);
    expect($donald->role)->toBe('staff');

    $leo = User::where('email', 'leo@smartbiz.com')->first();
    expect($leo)->not->toBeNull();

    // Check first client created
    $client = Client::where('name', 'Ok Tedi Mining')->first();
    expect($client)->not->toBeNull();
    expect($client->company_id)->toBe($this->company->id);
    expect($client->contact_person)->toBe('Primary Contact');
    expect($client->status)->toBe('Active');

    // Check first WIP job created and assigned
    $wip = Wip::where('name', 'Quarterly Audit Review Q2')->first();
    expect($wip)->not->toBeNull();
    expect($wip->company_id)->toBe($this->company->id);
    expect($wip->client_id)->toBe($client->id);
    expect((float)$wip->estimated_fee)->toBe(7500.00);
    expect($wip->next_action)->toBe('Gather draft financial ledgers');

    // Check progress update was logged
    $update = ProgressUpdate::where('wip_id', $wip->id)->first();
    expect($update)->not->toBeNull();
    expect($update->percentage_completed)->toBe(10);

    // Check industry template task got seeded
    $seededGST = Wip::where('company_id', $this->company->id)
        ->where('name', 'Monthly GST Filing')
        ->first();
    expect($seededGST)->not->toBeNull();
    expect($seededGST->status)->toBe('Active');
});
