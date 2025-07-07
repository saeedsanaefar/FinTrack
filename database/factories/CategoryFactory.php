<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $incomeCategories = [
            'Salary', 'Freelance', 'Investment Returns', 'Business Income', 
            'Rental Income', 'Dividends', 'Interest', 'Bonus'
        ];
        
        $expenseCategories = [
            'Food & Dining', 'Transportation', 'Shopping', 'Entertainment',
            'Bills & Utilities', 'Healthcare', 'Education', 'Travel',
            'Home & Garden', 'Personal Care', 'Insurance', 'Taxes'
        ];
        
        $type = fake()->randomElement(['income', 'expense', 'both']);
        
        // Generate unique name by adding random suffix
        $baseName = '';
        if ($type === 'income') {
            $baseName = fake()->randomElement($incomeCategories);
        } elseif ($type === 'expense') {
            $baseName = fake()->randomElement($expenseCategories);
        } else {
            $baseName = fake()->randomElement(array_merge($incomeCategories, $expenseCategories));
        }
        
        // Add random suffix to ensure uniqueness
        $name = $baseName . ' ' . fake()->unique()->numberBetween(1, 9999);
        
        return [
            'user_id' => User::factory(),
            'name' => $name,
            'type' => $type,
            'color' => fake()->hexColor(),
            'icon' => fake()->randomElement([
                '💰', '🍔', '🚗', '🛍️', '🎬', '💡', '🏥', '📚', 
                '✈️', '🏠', '💄', '🛡️', '📊', '💳', '🎯'
            ]),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the category is for income.
     */
    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'income',
            'name' => fake()->randomElement([
                'Salary', 'Freelance', 'Investment Returns', 'Business Income', 
                'Rental Income', 'Dividends', 'Interest', 'Bonus'
            ]) . ' ' . fake()->unique()->numberBetween(1, 9999),
        ]);
    }

    /**
     * Indicate that the category is for expenses.
     */
    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'expense',
            'name' => fake()->randomElement([
                'Food & Dining', 'Transportation', 'Shopping', 'Entertainment',
                'Bills & Utilities', 'Healthcare', 'Education', 'Travel'
            ]) . ' ' . fake()->unique()->numberBetween(1, 9999),
        ]);
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}