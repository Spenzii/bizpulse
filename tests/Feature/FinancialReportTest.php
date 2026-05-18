<?php

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Wip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::create([
        'name' => 'Apex Advisory',
        'slug' => 'apex-advisory',
    ]);

    $this->admin = User::create([
        'company_id' => $this->company->id,
        'name' => 'Admin User',
        'email' => 'admin@apex.com',
        'password' => bcrypt('password'),
        'role' => 'company_admin',
    ]);

    $this->staff = User::create([
        'company_id' => $this->company->id,
        'name' => 'Staff Member',
        'email' => 'staff@apex.com',
        'password' => bcrypt('password'),
        'role' => 'staff',
    ]);

    $this->client = Client::create([
        'company_id' => $this->company->id,
        'name' => 'PNG Mining Ltd',
        'email' => 'finance@pngmining.com',
    ]);
});

test('non-admin staff cannot view financial report', function () {
    $response = $this->actingAs($this->staff)
        ->get(route('reports.financial'));

    $response->assertStatus(403);
});

test('admin can view financial report and values are calculated correctly', function () {
    Carbon::setTestNow(Carbon::create(2026, 5, 17, 12, 0, 0));

    // 1. Create invoices with varying issue dates to test aging buckets
    // Bracket 0-30 Days: Issued 10 days ago (2026-05-07)
    $inv1 = Invoice::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'invoice_number' => 'INV-2026-0001',
        'total_amount' => 1100,
        'tax_amount' => 100,
        'due_date' => '2026-05-21',
        'status' => 'Sent',
    ]);
    DB::table('invoices')->where('id', $inv1->id)->update(['created_at' => Carbon::now()->subDays(10)]);

    // Bracket 31-60 Days: Issued 45 days ago (2026-04-02)
    $inv2 = Invoice::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'invoice_number' => 'INV-2026-0002',
        'total_amount' => 2200,
        'tax_amount' => 200,
        'due_date' => '2026-04-16',
        'status' => 'Sent',
    ]);
    DB::table('invoices')->where('id', $inv2->id)->update(['created_at' => Carbon::now()->subDays(45)]);

    // Bracket 61-90 Days: Issued 75 days ago
    $inv3 = Invoice::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'invoice_number' => 'INV-2026-0003',
        'total_amount' => 3300,
        'tax_amount' => 300,
        'due_date' => '2026-03-15',
        'status' => 'Sent',
    ]);
    DB::table('invoices')->where('id', $inv3->id)->update(['created_at' => Carbon::now()->subDays(75)]);

    // Bracket 90+ Days: Issued 100 days ago
    $inv4 = Invoice::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'invoice_number' => 'INV-2026-0004',
        'total_amount' => 4400,
        'tax_amount' => 400,
        'due_date' => '2026-02-15',
        'status' => 'Sent',
    ]);
    DB::table('invoices')->where('id', $inv4->id)->update(['created_at' => Carbon::now()->subDays(100)]);

    // Paid invoice (should not be in outstanding aging buckets)
    $invPaid = Invoice::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'invoice_number' => 'INV-2026-0005',
        'total_amount' => 5500,
        'tax_amount' => 500,
        'due_date' => '2026-05-15',
        'status' => 'Paid',
    ]);
    DB::table('invoices')->where('id', $invPaid->id)->update(['created_at' => Carbon::now()->subDays(5)]);

    // 2. Access the financial report
    $response = $this->actingAs($this->admin)
        ->get(route('reports.financial'));

    $response->assertOk();

    // 3. Verify Inertia prop calculations
    $metrics = $response->original->getData()['page']['props']['metrics'];
    $aging = $response->original->getData()['page']['props']['agingBuckets'];

    // Total Billed = Sent + Paid = 1100 + 2200 + 3300 + 4400 + 5500 = 16500
    expect($metrics['total_billed'])->toEqual(16500);
    // Total Collected = Paid = 5500
    expect($metrics['total_collected'])->toEqual(5500);
    // Outstanding = Sent = 1100 + 2200 + 3300 + 4400 = 11000
    expect($metrics['outstanding_receivables'])->toEqual(11000);

    // Aging Buckets verification
    expect((float)$aging['current'])->toEqual(1100.0);
    expect((float)$aging['bracket_30_60'])->toEqual(2200.0);
    expect((float)$aging['bracket_60_90'])->toEqual(3300.0);
    expect((float)$aging['bracket_90_plus'])->toEqual(4400.0);

    Carbon::setTestNow(); // Reset test time
});

test('admin can download general financial csv export', function () {
    $invoice = Invoice::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'invoice_number' => 'INV-2026-0001',
        'total_amount' => 1100,
        'tax_amount' => 100,
        'due_date' => '2026-05-30',
        'status' => 'Sent',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('exports.financial'));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    $response->assertHeader('Content-Disposition', 'attachment; filename=financial_invoice_report_' . now()->format('Y-m-d') . '.csv');

    $content = $response->streamedContent();
    expect($content)->toContain('Invoice Number');
    expect($content)->toContain('Client Name');
    expect($content)->toContain('Subtotal (PGK)');
    expect($content)->toContain('INV-2026-0001');
    expect($content)->toContain('PNG Mining Ltd');
});

test('admin can download xero import csv format', function () {
    $invoice = Invoice::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'invoice_number' => 'XERO-999',
        'total_amount' => 1100,
        'tax_amount' => 100,
        'due_date' => '2026-06-01',
        'status' => 'Sent',
    ]);

    $item = InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'description' => 'Development Services',
        'amount' => 1000,
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('exports.xero'));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    
    $content = $response->streamedContent();
    expect($content)->toContain('*ContactName');
    expect($content)->toContain('EmailAddress');
    expect($content)->toContain('*InvoiceNumber');
    expect($content)->toContain('PNG Mining Ltd');
    expect($content)->toContain('XERO-999');
    expect($content)->toContain('Development Services');
    expect($content)->toContain('1000.00');
    expect($content)->toContain('GST on Income');
});

test('staff metrics velocity and realization rates calculate correctly', function () {
    Carbon::setTestNow(Carbon::create(2026, 5, 17, 12, 0, 0));

    // Create 2 completed WIPs for the staff member to test velocity (one took 5 days, one took 15 days, average should be 10 days)
    $wip1 = Wip::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'assigned_to_id' => $this->staff->id,
        'name' => 'WIP One',
        'estimated_fee' => 1000,
        'due_date' => '2026-05-20',
        'status' => 'Completed',
        'billing_status' => 'Invoiced',
        'completed_at' => Carbon::now()->subDays(15), // Took 5 days
    ]);
    DB::table('wips')->where('id', $wip1->id)->update(['created_at' => Carbon::now()->subDays(20)]);

    $wip2 = Wip::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'assigned_to_id' => $this->staff->id,
        'name' => 'WIP Two',
        'estimated_fee' => 2000,
        'due_date' => '2026-05-20',
        'status' => 'Completed',
        'billing_status' => 'Invoiced',
        'completed_at' => Carbon::now()->subDays(15), // Took 15 days
    ]);
    DB::table('wips')->where('id', $wip2->id)->update(['created_at' => Carbon::now()->subDays(30)]);

    // Create invoice and items to test fee realization rate:
    // Wip1 estimated fee = 1000. Actual invoiced amount = 1100 (110% realization)
    // Wip2 estimated fee = 2000. Actual invoiced amount = 1900 (95% realization)
    // Total estimated = 3000. Total invoiced = 3000 (100% total realization rate)
    $invoice = Invoice::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'invoice_number' => 'INV-REAL',
        'total_amount' => 3300,
        'tax_amount' => 300,
        'due_date' => '2026-05-30',
        'status' => 'Sent',
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'wip_id' => $wip1->id,
        'description' => 'WIP One Item',
        'amount' => 1100,
    ]);

    InvoiceItem::create([
        'invoice_id' => $invoice->id,
        'wip_id' => $wip2->id,
        'description' => 'WIP Two Item',
        'amount' => 1900,
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('reports.relationship-owners'));

    $response->assertOk();

    $reportData = $response->original->getData()['page']['props']['reportData'];
    $staffData = collect($reportData)->firstWhere('id', $this->staff->id);

    expect($staffData)->not->toBeNull();
    // Avg days = (5 + 15) / 2 = 10
    expect((float)$staffData['avg_completion_days'])->toEqual(10.0);
    // Realization rate = (3000 / 3000) * 100 = 100%
    expect((float)$staffData['realization_rate'])->toEqual(100.0);

    Carbon::setTestNow(); // Reset time
});
