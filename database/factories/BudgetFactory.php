<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
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
            'category_id' => Category::factory()->expense(),
            'amount' => fake()->randomFloat(2, 100, 2000),
            'spent_amount' => 0.00,
            'year' => now()->year,
            'month' => fake()->numberBetween(1, 12),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the budget is for the current month.
     */
    public function currentMonth(): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => now()->year,
            'month' => now()->month,
        ]);
    }

    /**
     * Indicate that the budget is for the previous month.
     */
    public function previousMonth(): static
    {
        $previousMonth = now()->subMonth();
        return $this->state(fn (array $attributes) => [
            'year' => $previousMonth->year,
            'month' => $previousMonth->month,
        ]);
    }

    /**
     * Indicate that the budget is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set a specific budget amount.
     */
    public function withAmount(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }

    /**
     * Set a specific spent amount.
     */
    public function withSpentAmount(float $spentAmount): static
    {
        return $this->state(fn (array $attributes) => [
            'spent_amount' => $spentAmount,
        ]);
    }

    /**
     * Create a budget that's over the limit.
     */
    public function overBudget(): static
    {
        return $this->state(function (array $attributes) {
            $amount = fake()->randomFloat(2, 500, 1000);
            return [
                'amount' => $amount,
                'spent_amount' => $amount + fake()->randomFloat(2, 50, 200),
            ];
        });
    }

    /**
     * Create a budget that's nearly at the limit.
     */
    public function nearLimit(): static
    {
        return $this->state(function (array $attributes) {
            $amount = fake()->randomFloat(2, 500, 1000);
            return [
                'amount' => $amount,
                'spent_amount' => $amount * 0.9, // 90% of budget
            ];
        });
    }

    /**
     * Create a budget for a specific year and month.
     */
    public function forPeriod(int $year, int $month): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => $year,
            'month' => $month,
        ]);
    }
}