<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Super Admin
        User::updateOrCreate(
            ['email' => 'smark@penguinpacificltd.com'],
            [
                'name' => 'Stanley Spenzii Mark',
                'password' => bcrypt('B!zzPulz@Espenz!!369'),
                'role' => 'super_admin',
            ]
        );

        // 2. Seed Subscription Plans
        $plans = [
            [
                'name' => 'Starter',
                'monthly_fee' => 149.00,
                'setup_fee' => 1500.00,
                'training_fee' => 500.00,
                'user_limit' => 5,
                'features' => ['Core Workflow', 'Task Tracking', 'Basic Reports'],
                'is_recommended' => false,
            ],
            [
                'name' => 'Business',
                'monthly_fee' => 399.00,
                'setup_fee' => 2500.00,
                'training_fee' => 1000.00,
                'user_limit' => 15,
                'features' => ['Advanced Workflow', 'Billing Management', 'Weekly Reviews'],
                'is_recommended' => true,
            ],
            [
                'name' => 'Professional',
                'monthly_fee' => 899.00,
                'setup_fee' => 5000.00,
                'training_fee' => 2000.00,
                'user_limit' => 50,
                'features' => ['Full SaaS Suite', 'Dedicated Support', 'Custom Templates'],
                'is_recommended' => false,
            ],
        ];

        foreach ($plans as $planData) {
            \App\Models\SubscriptionPlan::updateOrCreate(['name' => $planData['name']], $planData);
        }

        // 3. Seed Initial CMS Sections
        $sections = [
            ['name' => 'Hero', 'key' => 'hero', 'content' => [
                'headline' => 'BizPulse: The Pulse of Your Business',
                'subheadline' => 'Simple client, task, and staff accountability for PNG and Pacific businesses.',
                'cta_text' => 'Get Started',
                'cta_link' => '/register'
            ]],
            ['name' => 'Pricing', 'key' => 'pricing', 'content' => [
                'title' => 'Transparent Pricing for Growth',
                'description' => 'Choose the plan that fits your SME size.'
            ]],
        ];

        foreach ($sections as $sectionData) {
            \App\Models\CmsSection::updateOrCreate(['key' => $sectionData['key']], $sectionData);
        }
    }
}
