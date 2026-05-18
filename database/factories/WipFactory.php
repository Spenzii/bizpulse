<?php

namespace Database\Factories;

use App\Models\Wip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Wip>
 */
class WipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'client_id' => \App\Models\Client::factory(),
            'assigned_to_id' => \App\Models\User::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'estimated_fee' => $this->faker->randomFloat(2, 100, 5000),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => 'Active',
            'billing_status' => 'Not Ready',
            'is_recurring' => false,
        ];
    }
}
