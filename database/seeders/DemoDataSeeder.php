<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\Client;
use App\Models\Wip;
use App\Models\Blocker;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Company
        $company = Company::updateOrCreate(
            ['slug' => 'smartbiz'],
            [
                'name' => 'SmartBiz Pacific',
                'subscription_plan' => 'Business',
                'subscription_status' => 'Active',
            ]
        );

        // 2. Create Company Admin
        $admin = User::updateOrCreate(
            ['email' => 'john@smartbiz.com'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password'),
                'role' => 'company_admin',
                'company_id' => $company->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'SmartBiz Admin',
                'password' => bcrypt('password'),
                'role' => 'company_admin',
                'company_id' => $company->id,
            ]
        );

        // 3. Create Staff
        $staff1 = User::updateOrCreate(
            ['email' => 'sarah@smartbiz.com'],
            [
                'name' => 'Sarah Kapi',
                'password' => bcrypt('password'),
                'role' => 'staff',
                'company_id' => $company->id,
            ]
        );

        $staff2 = User::updateOrCreate(
            ['email' => 'michael@smartbiz.com'],
            [
                'name' => 'Michael Tom',
                'password' => bcrypt('password'),
                'role' => 'staff',
                'company_id' => $company->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Demo Staff',
                'password' => bcrypt('password'),
                'role' => 'staff',
                'company_id' => $company->id,
            ]
        );

        // 4. Create Clients
        $client1 = Client::updateOrCreate(
            ['name' => 'Port Moresby Port Services', 'company_id' => $company->id],
            [
                'contact_person' => 'James Wong',
                'relationship_owner_id' => $admin->id,
            ]
        );

        $client2 = Client::updateOrCreate(
            ['name' => 'Pacific Energy Ltd', 'company_id' => $company->id],
            [
                'contact_person' => 'Anna Maru',
                'relationship_owner_id' => $admin->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'client@example.com'],
            [
                'name' => 'Demo Client',
                'password' => bcrypt('password'),
                'role' => 'client',
                'company_id' => $company->id,
                'client_id' => $client1->id,
            ]
        );

        // 5. Create WIPs
        Wip::updateOrCreate(
            ['name' => 'Annual Audit 2025', 'company_id' => $company->id],
            [
                'client_id' => $client1->id,
                'assigned_to_id' => $staff1->id,
                'due_date' => now()->addDays(10),
                'status' => 'Active',
                'estimated_fee' => 15000.00,
                'next_action' => 'Collect bank statements',
            ]
        );

        Wip::updateOrCreate(
            ['name' => 'Quarterly Tax Filing', 'company_id' => $company->id],
            [
                'client_id' => $client1->id,
                'assigned_to_id' => $staff2->id,
                'due_date' => now()->subDays(2),
                'status' => 'Active',
                'estimated_fee' => 2500.00,
                'next_action' => 'Follow up with IRC',
            ]
        );

        $wip3 = Wip::updateOrCreate(
            ['name' => 'Business Advisory Session', 'company_id' => $company->id],
            [
                'client_id' => $client2->id,
                'assigned_to_id' => $staff1->id,
                'due_date' => now()->addDays(5),
                'status' => 'Blocked',
                'estimated_fee' => 5000.00,
                'next_action' => 'Wait for financial history doc',
            ]
        );

        Wip::updateOrCreate(
            ['name' => 'Company Registration Update', 'company_id' => $company->id],
            [
                'client_id' => $client2->id,
                'assigned_to_id' => $admin->id,
                'due_date' => now()->subDays(1),
                'status' => 'Active',
                'estimated_fee' => 1200.00,
                'billing_status' => 'Ready to Invoice',
            ]
        );

        Wip::updateOrCreate(
            ['name' => 'Compliance Review', 'company_id' => $company->id],
            [
                'client_id' => $client2->id,
                'assigned_to_id' => $staff1->id,
                'due_date' => now()->addDays(30),
                'status' => 'Completed',
                'estimated_fee' => 3500.00,
                'billing_status' => 'Ready to Invoice',
            ]
        );

        // 6. Create Blocker
        Blocker::updateOrCreate(
            ['wip_id' => $wip3->id, 'company_id' => $company->id],
            [
                'raised_by_id' => $staff1->id,
                'description' => 'Awaiting certificate of incorporation from Investment Promotion Authority.',
                'status' => 'Open',
            ]
        );
    }
}
