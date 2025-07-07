<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['income', 'expense', 'transfer']);
        
        return [
            'user_id' => User::factory(),
            'account_id' => Account::factory(),
            'category_id' => $type !== 'transfer' ? Category::factory() : null,
            'transfer_account_id' => $type === 'transfer' ? Account::factory() : null,
            'description' => fake()->sentence(3),
            'type' => $type,
            'amount' => fake()->randomFloat(2, 10, 1000),
            'date' => fake()->dateTimeThisYear(),
            'reference' => fake()->optional(0.3)->bothify('REF-####-????'),
            'notes' => fake()->optional(0.4)->paragraph(1),
            'is_recurring' => false,
        ];
    }

    /**
     * Indicate that the transaction is an income.
     */
    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'income',
            'category_id' => Category::factory()->income(),
            'transfer_account_id' => null,
            'amount' => fake()->randomFloat(2, 100, 5000),
            'description' => fake()->randomElement([
                'Salary Payment',
                'Freelance Project',
                'Investment Dividend',
                'Bonus Payment',
                'Rental Income'
            ]),
        ]);
    }

    /**
     * Indicate that the transaction is an expense.
     */
    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'expense',
            'category_id' => Category::factory()->expense(),
            'transfer_account_id' => null,
            'amount' => fake()->randomFloat(2, 5, 500),
            'description' => fake()->randomElement([
                'Grocery Shopping',
                'Gas Station',
                'Restaurant Bill',
                'Utility Payment',
                'Online Purchase'
            ]),
        ]);
    }

    /**
     * Indicate that the transaction is a transfer.
     */
    public function transfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'transfer',
            'category_id' => null,
            'transfer_account_id' => Account::factory(),
            'amount' => fake()->randomFloat(2, 50, 2000),
            'description' => 'Account Transfer',
        ]);
    }

    /**
     * Indicate that the transaction is recurring.
     */
    public function recurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
        ]);
    }

    /**
     * Set a specific amount for the transaction.
     */
    public function withAmount(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }

    /**
     * Set a specific date for the transaction.
     */
    public function onDate(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => $date,
        ]);
    }

    /**
     * Create transaction for this month.
     */
    public function thisMonth(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => fake()->dateTimeThisMonth(),
        ]);
    }

    /**
     * Create transaction for this year.
     */
    public function thisYear(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => fake()->dateTimeThisYear(),
        ]);
    }
}