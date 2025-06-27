<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Command;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;

class DefaultCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users to create default categories for each
        $users = User::all();

        foreach ($users as $user) {
            $this->createDefaultCategoriesForUser($user);
        }
    }

    /**
     * Create default categories for a specific user
     */
    public function createDefaultCategoriesForUser(User $user): void
    {
        $defaultCategories = [
            // Income Categories
            [
                'name' => 'Salary',
                'type' => 'income',
                'icon' => 'fas fa-briefcase',
                'color' => '#10B981',
                'description' => 'Regular salary and wages',
                'sort_order' => 1,
            ],
            [
                'name' => 'Freelance',
                'type' => 'income',
                'icon' => 'fas fa-laptop-code',
                'color' => '#3B82F6',
                'description' => 'Freelance work and consulting',
                'sort_order' => 2,
            ],
            [
                'name' => 'Investment',
                'type' => 'income',
                'icon' => 'fas fa-chart-line',
                'color' => '#8B5CF6',
                'description' => 'Investment returns and dividends',
                'sort_order' => 3,
            ],
            [
                'name' => 'Business',
                'type' => 'income',
                'icon' => 'fas fa-store',
                'color' => '#F59E0B',
                'description' => 'Business income and profits',
                'sort_order' => 4,
            ],
            [
                'name' => 'Other Income',
                'type' => 'income',
                'icon' => 'fas fa-plus-circle',
                'color' => '#06B6D4',
                'description' => 'Other sources of income',
                'sort_order' => 5,
            ],

            // Expense Categories
            [
                'name' => 'Food & Dining',
                'type' => 'expense',
                'icon' => 'fas fa-utensils',
                'color' => '#EF4444',
                'description' => 'Restaurants, groceries, and food expenses',
                'sort_order' => 1,
            ],
            [
                'name' => 'Transportation',
                'type' => 'expense',
                'icon' => 'fas fa-car',
                'color' => '#F97316',
                'description' => 'Gas, public transport, car maintenance',
                'sort_order' => 2,
            ],
            [
                'name' => 'Shopping',
                'type' => 'expense',
                'icon' => 'fas fa-shopping-bag',
                'color' => '#EC4899',
                'description' => 'Clothing, electronics, and general shopping',
                'sort_order' => 3,
            ],
            [
                'name' => 'Entertainment',
                'type' => 'expense',
                'icon' => 'fas fa-film',
                'color' => '#8B5CF6',
                'description' => 'Movies, games, hobbies, and entertainment',
                'sort_order' => 4,
            ],
            [
                'name' => 'Bills & Utilities',
                'type' => 'expense',
                'icon' => 'fas fa-file-invoice-dollar',
                'color' => '#6B7280',
                'description' => 'Electricity, water, internet, phone bills',
                'sort_order' => 5,
            ],
            [
                'name' => 'Healthcare',
                'type' => 'expense',
                'icon' => 'fas fa-heartbeat',
                'color' => '#DC2626',
                'description' => 'Medical expenses, insurance, pharmacy',
                'sort_order' => 6,
            ],
            [
                'name' => 'Education',
                'type' => 'expense',
                'icon' => 'fas fa-graduation-cap',
                'color' => '#2563EB',
                'description' => 'Tuition, books, courses, and education',
                'sort_order' => 7,
            ],
            [
                'name' => 'Travel',
                'type' => 'expense',
                'icon' => 'fas fa-plane',
                'color' => '#059669',
                'description' => 'Vacation, business trips, and travel expenses',
                'sort_order' => 8,
            ],
            [
                'name' => 'Home & Garden',
                'type' => 'expense',
                'icon' => 'fas fa-home',
                'color' => '#7C3AED',
                'description' => 'Rent, mortgage, home improvement, gardening',
                'sort_order' => 9,
            ],
            [
                'name' => 'Personal Care',
                'type' => 'expense',
                'icon' => 'fas fa-spa',
                'color' => '#DB2777',
                'description' => 'Haircuts, cosmetics, personal hygiene',
                'sort_order' => 10,
            ],
            [
                'name' => 'Gifts & Donations',
                'type' => 'expense',
                'icon' => 'fas fa-gift',
                'color' => '#DC2626',
                'description' => 'Gifts, charity, and donations',
                'sort_order' => 11,
            ],
            [
                'name' => 'Taxes',
                'type' => 'expense',
                'icon' => 'fas fa-receipt',
                'color' => '#374151',
                'description' => 'Income tax, property tax, and other taxes',
                'sort_order' => 12,
            ],
            [
                'name' => 'Insurance',
                'type' => 'expense',
                'icon' => 'fas fa-shield-alt',
                'color' => '#1F2937',
                'description' => 'Life, health, car, and other insurance',
                'sort_order' => 13,
            ],
            [
                'name' => 'Other Expenses',
                'type' => 'expense',
                'icon' => 'fas fa-minus-circle',
                'color' => '#6B7280',
                'description' => 'Miscellaneous and other expenses',
                'sort_order' => 14,
            ],
        ];

        foreach ($defaultCategories as $categoryData) {
            // Check if category already exists for this user
            $existingCategory = Category::where('user_id', $user->id)
                ->where('name', $categoryData['name'])
                ->where('type', $categoryData['type'])
                ->first();

            if (!$existingCategory) {
                Category::create(array_merge($categoryData, [
                    'user_id' => $user->id,
                    'is_active' => true,
                ]));
            }
        }

        $this->command->info("Default categories created for user: {$user->name}");
    }
}