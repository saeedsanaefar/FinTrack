<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Budget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    public function test_category_belongs_to_user(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $this->assertInstanceOf(User::class, $category->user);
        $this->assertEquals($this->user->id, $category->user->id);
    }

    public function test_category_has_many_transactions(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        
        Transaction::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
        ]);

        $this->assertCount(3, $category->transactions);
        $this->assertInstanceOf(Transaction::class, $category->transactions->first());
    }

    public function test_category_has_many_budgets(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        
        Budget::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
        ]);

        $this->assertCount(2, $category->budgets);
        $this->assertInstanceOf(Budget::class, $category->budgets->first());
    }

    public function test_category_is_income_attribute(): void
    {
        $incomeCategory = Category::factory()->make(['type' => 'income']);
        $expenseCategory = Category::factory()->make(['type' => 'expense']);

        $this->assertTrue($incomeCategory->getIsIncomeAttribute());
        $this->assertFalse($expenseCategory->getIsIncomeAttribute());
    }

    public function test_category_is_expense_attribute(): void
    {
        $incomeCategory = Category::factory()->make(['type' => 'income']);
        $expenseCategory = Category::factory()->make(['type' => 'expense']);

        $this->assertFalse($incomeCategory->getIsExpenseAttribute());
        $this->assertTrue($expenseCategory->getIsExpenseAttribute());
    }

    public function test_category_is_active_attribute(): void
    {
        $activeCategory = Category::factory()->make(['is_active' => true]);
        $inactiveCategory = Category::factory()->make(['is_active' => false]);

        $this->assertTrue($activeCategory->getIsActiveAttribute());
        $this->assertFalse($inactiveCategory->getIsActiveAttribute());
    }

    public function test_category_scope_active(): void
    {
        Category::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        Category::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => false,
        ]);

        $activeCategories = Category::active()->get();

        $this->assertCount(1, $activeCategories);
        $this->assertTrue($activeCategories->first()->is_active);
    }

    public function test_category_scope_by_type(): void
    {
        Category::factory()->income()->create(['user_id' => $this->user->id]);
        Category::factory()->expense()->create(['user_id' => $this->user->id]);

        $incomeCategories = Category::byType('income')->get();
        $expenseCategories = Category::byType('expense')->get();

        $this->assertCount(1, $incomeCategories);
        $this->assertCount(1, $expenseCategories);
        $this->assertEquals('income', $incomeCategories->first()->type);
        $this->assertEquals('expense', $expenseCategories->first()->type);
    }

    public function test_category_scope_for_user(): void
    {
        $otherUser = User::factory()->create();

        Category::factory()->create(['user_id' => $this->user->id]);
        Category::factory()->create(['user_id' => $otherUser->id]);

        $userCategories = Category::forUser($this->user->id)->get();

        $this->assertCount(1, $userCategories);
        $this->assertEquals($this->user->id, $userCategories->first()->user_id);
    }

    public function test_category_total_amount_calculation(): void
    {
        $category = Category::factory()->expense()->create(['user_id' => $this->user->id]);
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 100.00,
        ]);

        $this->assertEquals(300.00, $category->getTotalAmountAttribute());
    }

    public function test_category_monthly_total_calculation(): void
    {
        $category = Category::factory()->expense()->create(['user_id' => $this->user->id]);
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        // Current month transactions
        Transaction::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 150.00,
            'date' => Carbon::now(),
        ]);

        // Previous month transaction (should not be included)
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 200.00,
            'date' => Carbon::now()->subMonth(),
        ]);

        $this->assertEquals(300.00, $category->getMonthlyTotalAttribute());
    }

    public function test_category_transaction_count(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
        ]);

        $this->assertEquals(5, $category->getTransactionCountAttribute());
    }

    public function test_category_average_transaction_amount(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => 100.00,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => 200.00,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => 300.00,
        ]);

        $this->assertEquals(200.00, $category->getAverageTransactionAmountAttribute());
    }

    public function test_category_last_transaction_date(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        $lastTransactionDate = Carbon::now()->subDays(2);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'date' => Carbon::now()->subDays(5),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'date' => $lastTransactionDate,
        ]);

        $this->assertEquals(
            $lastTransactionDate->format('Y-m-d'),
            $category->getLastTransactionDateAttribute()->format('Y-m-d')
        );
    }

    public function test_category_can_be_deleted_when_no_transactions(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $this->assertTrue($category->canBeDeleted());
    }

    public function test_category_cannot_be_deleted_when_has_transactions(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
        ]);

        $this->assertFalse($category->canBeDeleted());
    }

    public function test_category_cannot_be_deleted_when_has_budgets(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
        ]);

        $this->assertFalse($category->canBeDeleted());
    }

    public function test_category_color_validation(): void
    {
        $category = Category::factory()->make(['color' => '#FF5733']);

        $this->assertTrue($category->isValidColor());
    }

    public function test_category_invalid_color_validation(): void
    {
        $category = Category::factory()->make(['color' => 'invalid-color']);

        $this->assertFalse($category->isValidColor());
    }

    public function test_category_icon_attribute(): void
    {
        $category = Category::factory()->make(['icon' => 'shopping-cart']);

        $this->assertEquals('shopping-cart', $category->icon);
    }

    public function test_category_formatted_color_attribute(): void
    {
        $category = Category::factory()->make(['color' => '#FF5733']);

        $this->assertEquals('#FF5733', $category->getFormattedColorAttribute());
    }

    public function test_category_deactivation(): void
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        $category->deactivate();

        $this->assertFalse($category->is_active);
    }

    public function test_category_reactivation(): void
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => false,
        ]);

        $category->activate();

        $this->assertTrue($category->is_active);
    }

    public function test_category_usage_statistics(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        // Create transactions over different months
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => 100.00,
            'date' => Carbon::now(),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'amount' => 150.00,
            'date' => Carbon::now()->subMonth(),
        ]);

        $stats = $category->getUsageStatistics();

        $this->assertArrayHasKey('total_transactions', $stats);
        $this->assertArrayHasKey('total_amount', $stats);
        $this->assertArrayHasKey('average_amount', $stats);
        $this->assertEquals(2, $stats['total_transactions']);
        $this->assertEquals(250.00, $stats['total_amount']);
        $this->assertEquals(125.00, $stats['average_amount']);
    }

    public function test_category_budget_status(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
            'amount' => 1000.00,
            'spent_amount' => 800.00,
            'year' => Carbon::now()->year,
            'month' => Carbon::now()->month,
        ]);

        $this->assertTrue($category->hasActiveBudget());
        $this->assertEquals($budget->id, $category->getCurrentBudget()->id);
    }

    public function test_category_spending_trend(): void
    {
        $category = Category::factory()->expense()->create(['user_id' => $this->user->id]);
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        // Current month spending
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 500.00,
            'date' => Carbon::now(),
        ]);

        // Previous month spending
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 300.00,
            'date' => Carbon::now()->subMonth(),
        ]);

        $trend = $category->getSpendingTrend();

        $this->assertArrayHasKey('current_month', $trend);
        $this->assertArrayHasKey('previous_month', $trend);
        $this->assertArrayHasKey('percentage_change', $trend);
        $this->assertEquals(500.00, $trend['current_month']);
        $this->assertEquals(300.00, $trend['previous_month']);
    }
}