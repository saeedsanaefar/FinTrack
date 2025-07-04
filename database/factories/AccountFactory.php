<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement([
                'Checking Account',
                'Savings Account',
                'Credit Card',
                'Cash Wallet',
                'Investment Account',
                'Business Account'
            ]),
            'type' => fake()->randomElement(['checking', 'savings', 'credit_card', 'cash', 'investment']),
            'balance' => fake()->randomFloat(2, 0, 10000),
            'currency' => 'USD',
            'is_active' => true,
            'description' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the account is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the account has a specific balance.
     */
    public function withBalance(float $balance): static
    {
        return $this->state(fn (array $attributes) => [
            'balance' => $balance,
        ]);
    }

    /**
     * Indicate that the account is a credit card.
     */
    public function creditCard(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'credit',
            'name' => fake()->randomElement(['Visa Card', 'MasterCard', 'American Express', 'Discover Card']),
            'balance' => fake()->randomFloat(2, -5000, 0), // Credit cards typically have negative balances
        ]);
    }
}
