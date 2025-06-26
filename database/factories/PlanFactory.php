<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return  [
            'title' => fake()->unique()->randomElement(['Starter', 'Enterprise', 'Basic']),
            'description' => fake()->sentence,
            'days' => fake()->randomElement([30, 365]),
            'price' => fake()->randomFloat(2, 0, 1000),
            'data' => [
                'cards' => ['value' => $cards = fake()->numberBetween(5, 100), 'overview' => 'Credit: ' . $cards],
                'deposit_fee' => ['value' => $deposit_fee = fake()->numberBetween(5, 10), 'overview' => 'deposit fee: ' . $deposit_fee],
                'transaction_fee' => ['value' => $transaction_fee = fake()->numberBetween(5, 10), 'overview' => 'transaction fee: ' . $transaction_fee],
                'service_fee' => ['value' => $service_fee = fake()->numberBetween(5, 10), 'overview' => 'service fee: ' . $service_fee],
            ],
            'is_featured' => fake()->boolean,
            'is_recommended' => fake()->boolean,
            'is_trial' => fake()->boolean,
            'status' => fake()->boolean,
            'trial_days' => fake()->numberBetween(0, 30)
        ];
    }
}
