<?php

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use App\Models\Wip;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::create([
        'name' => 'Test Company',
        'slug' => 'test-company',
        'subscription_status' => 'Active',
    ]);

    $this->admin = User::create([
        'company_id' => $this->company->id,
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'role' => 'company_admin',
    ]);

    $this->client = Client::create([
        'company_id' => $this->company->id,
        'name' => 'Test Client',
    ]);

    $this->wip = Wip::factory()->create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'billing_status' => 'Ready to Invoice',
        'estimated_fee' => 1000,
    ]);
});

test('admin can generate an invoice from a WIP', function () {
    $this->withoutExceptionHandling();
    $response = $this->actingAs($this->admin)
        ->post(route('invoices.store'), [
            'client_id' => $this->client->id,
            'wip_ids' => [$this->wip->id],
            'due_date' => now()->addDays(14)->toDateString(),
        ]);

    $response->assertSessionHasNoErrors();
    
    $invoice = Invoice::first();
    expect($invoice)->not->toBeNull();
    
    $response->assertRedirect(route('invoices.show', $invoice->id));

    expect((float)$invoice->total_amount)->toEqual(1100.00); // 1000 + 10% tax
});

test('client can view their own invoice', function () {
    $invoice = Invoice::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'invoice_number' => 'INV-001',
        'total_amount' => 1100,
        'tax_amount' => 100,
        'due_date' => now()->addDays(14)->toDateString(),
        'status' => 'Draft',
    ]);

    $clientUser = User::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'name' => 'Client User',
        'email' => 'client@example.com',
        'password' => bcrypt('password'),
        'role' => 'client',
    ]);

    $this->actingAs($clientUser)
        ->get(route('invoices.show', $invoice->id))
        ->assertOk();
});

test('client cannot view another clients invoice', function () {
    $otherClient = Client::create([
        'company_id' => $this->company->id,
        'name' => 'Other Client',
    ]);

    $invoice = Invoice::create([
        'company_id' => $this->company->id,
        'client_id' => $otherClient->id,
        'invoice_number' => 'INV-001',
        'total_amount' => 1100,
        'tax_amount' => 100,
        'due_date' => now()->addDays(14)->toDateString(),
        'status' => 'Draft',
    ]);

    $clientUser = User::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'name' => 'Client User',
        'email' => 'client@example.com',
        'password' => bcrypt('password'),
        'role' => 'client',
    ]);

    $this->actingAs($clientUser)
        ->get(route('invoices.show', $invoice->id))
        ->assertStatus(403);
});
