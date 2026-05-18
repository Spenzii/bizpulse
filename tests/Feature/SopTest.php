<?php

use App\Models\Company;
use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::create([
        'name' => 'SmartBiz Pacific',
        'slug' => 'smartbiz-pacific',
        'onboarded' => true,
    ]);

    $this->staff = User::create([
        'company_id' => $this->company->id,
        'name' => 'Rosinta John',
        'email' => 'rosinta@example.com',
        'password' => bcrypt('password'),
        'role' => 'staff',
    ]);
});

test('guest is redirected to login when visiting operating rhythm', function () {
    $response = $this->get(route('operating-rhythm.index'));
    $response->assertRedirect(route('login'));
});

test('client is forbidden from visiting operating rhythm', function () {
    $client = Client::create([
        'company_id' => $this->company->id,
        'name' => 'External Client Co',
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
        ->get(route('operating-rhythm.index'));

    $response->assertStatus(403);
});

test('non-onboarded staff are redirected to onboarding when visiting operating rhythm', function () {
    $this->company->update(['onboarded' => false]);

    $response = $this->actingAs($this->staff)
        ->get(route('operating-rhythm.index'));

    $response->assertRedirect(route('onboarding.index'));
});

test('onboarded staff can view operating rhythm', function () {
    $response = $this->actingAs($this->staff)
        ->get(route('operating-rhythm.index'));

    $response->assertOk();
});
