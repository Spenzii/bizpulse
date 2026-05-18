<?php

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use App\Models\Invoice;
use App\Mail\InvoiceMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::create([
        'name' => 'Test Firm',
        'slug' => 'test-firm',
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
        'email' => 'client@example.com',
    ]);

    $this->invoice = Invoice::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'invoice_number' => 'INV-001',
        'total_amount' => 1100,
        'tax_amount' => 100,
        'due_date' => now()->addDays(14)->toDateString(),
        'status' => 'Draft',
    ]);
});

test('admin can send an invoice email to client', function () {
    Mail::fake();

    $response = $this->actingAs($this->admin)
        ->post(route('invoices.send', $this->invoice->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    Mail::assertQueued(InvoiceMail::class, function ($mail) {
        return $mail->hasTo('client@example.com') &&
               $mail->invoice->id === $this->invoice->id;
    });

    $this->invoice->refresh();
    expect($this->invoice->status)->toBe('Sent');
});
