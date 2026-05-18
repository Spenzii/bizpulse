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

    $this->admin = User::factory()->create([
        'company_id' => $this->company->id,
        'role' => 'company_admin',
    ]);

    $this->client = Client::create([
        'company_id' => $this->company->id,
        'name' => 'Test Client',
    ]);
});

test('wip sets completed_at when marked as completed', function () {
    $wip = Wip::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'name' => 'Test Job',
        'status' => 'Active',
        'due_date' => now()->addDays(7),
    ]);

    expect($wip->completed_at)->toBeNull();

    $wip->update(['status' => 'Completed']);

    expect($wip->fresh()->completed_at)->not->toBeNull();
    expect($wip->fresh()->completed_at->toDateString())->toBe(now()->toDateString());
});

test('bulk assign updates multiple wips', function () {
    $staff = User::factory()->create(['company_id' => $this->company->id, 'role' => 'staff']);
    
    $wips = Wip::factory()->count(3)->create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'assigned_to_id' => null
    ]);

    $this->actingAs($this->admin)
        ->post(route('wips.bulk-assign'), [
            'ids' => $wips->pluck('id')->toArray(),
            'assigned_to_id' => $staff->id,
        ])
        ->assertStatus(302);

    foreach ($wips as $wip) {
        expect($wip->fresh()->assigned_to_id)->toBe($staff->id);
    }
});

test('bulk duplicate rolls over jobs to next month', function () {
    $dueDate = now();
    $wip = Wip::create([
        'company_id' => $this->company->id,
        'client_id' => $this->client->id,
        'name' => 'Recurring Job',
        'status' => 'Completed',
        'due_date' => $dueDate,
        'completed_at' => now(),
        'is_recurring' => true,
    ]);

    $this->actingAs($this->admin)
        ->post(route('wips.bulk-duplicate'), [
            'ids' => [$wip->id],
        ])
        ->assertStatus(302);

    expect(Wip::count())->toBe(2);
    
    $newWip = Wip::where('id', '!=', $wip->id)->first();
    expect($newWip->status)->toBe('Active');
    expect($newWip->completed_at)->toBeNull();
    expect($newWip->due_date->toDateString())->toBe($dueDate->copy()->addMonth()->toDateString());
});
