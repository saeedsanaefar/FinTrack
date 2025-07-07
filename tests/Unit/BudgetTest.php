<?php

namespace Tests\Unit;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;
    protected Account $account;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->category = Category::factory()->expense()->create(['user_id' => $this->user->id]);
        $this->account = Account::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_budget_calculates_spent_amount_correctly(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 1000.00,
            'year' => now()->year,
            'month' => now()->month,
        ]);

        // Create transactions for this budget period
        Transaction::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 150.00,
            'date' => now()->startOfMonth()->addDays(5),
        ]);

        // Create transaction from different month (should not be included)
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 200.00,
            'date' => now()->subMonth(),
        ]);

        $this->assertEquals(450.00, $budget->calculateSpentAmount());
    }

    public function test_budget_progress_percentage_calculation(): void
    {
        $budget = Budget::factory()->make([
            'amount' => 1000.00,
            'spent_amount' => 250.00,
        ]);

        $this->assertEquals(25, $budget->getProgressPercentageAttribute());
    }

    public function test_budget_progress_percentage_with_zero_amount(): void
    {
        $budget = Budget::factory()->make([
            'amount' => 0,
            'spent_amount' => 100.00,
        ]);

        $this->assertEquals(0, $budget->getProgressPercentageAttribute());
    }

    public function test_budget_progress_percentage_over_100(): void
    {
        $budget = Budget::factory()->make([
            'amount' => 500.00,
            'spent_amount' => 750.00,
        ]);

        $this->assertEquals(150, $budget->getProgressPercentageAttribute());
    }

    public function test_budget_is_over_budget_detection(): void
    {
        $underBudget = Budget::factory()->make([
            'amount' => 1000.00,
            'spent_amount' => 800.00,
        ]);

        $overBudget = Budget::factory()->make([
            'amount' => 500.00,
            'spent_amount' => 600.00,
        ]);

        $exactBudget = Budget::factory()->make([
            'amount' => 1000.00,
            'spent_amount' => 1000.00,
        ]);

        $this->assertFalse($underBudget->getIsOverBudgetAttribute());
        $this->assertTrue($overBudget->getIsOverBudgetAttribute());
        $this->assertFalse($exactBudget->getIsOverBudgetAttribute());
    }

    public function test_budget_remaining_amount_calculation(): void
    {
        $budget = Budget::factory()->make([
            'amount' => 1000.00,
            'spent_amount' => 350.00,
        ]);

        $this->assertEquals(650.00, $budget->getRemainingAmountAttribute());
    }

    public function test_budget_remaining_amount_when_over_budget(): void
    {
        $budget = Budget::factory()->make([
            'amount' => 500.00,
            'spent_amount' => 750.00,
        ]);

        $this->assertEquals(-250.00, $budget->getRemainingAmountAttribute());
    }

    public function test_budget_is_near_limit_detection(): void
    {
        $nearLimit = Budget::factory()->make([
            'amount' => 1000.00,
            'spent_amount' => 900.00, // 90%
        ]);

        $notNearLimit = Budget::factory()->make([
            'amount' => 1000.00,
            'spent_amount' => 800.00, // 80%
        ]);

        $overBudget = Budget::factory()->make([
            'amount' => 1000.00,
            'spent_amount' => 1100.00, // 110%
        ]);

        $this->assertTrue($nearLimit->getIsNearLimitAttribute());
        $this->assertFalse($notNearLimit->getIsNearLimitAttribute());
        $this->assertFalse($overBudget->getIsNearLimitAttribute()); // Over budget, not just near
    }

    public function test_budget_status_attribute(): void
    {
        $onTrack = Budget::factory()->make([
            'amount' => 1000.00,
            'spent_amount' => 500.00, // 50%
        ]);

        $nearLimit = Budget::factory()->make([
            'amount' => 1000.00,
            'spent_amount' => 900.00, // 90%
        ]);

        $overBudget = Budget::factory()->make([
            'amount' => 1000.00,
            'spent_amount' => 1100.00, // 110%
        ]);

        $this->assertEquals('on_track', $onTrack->getStatusAttribute());
        $this->assertEquals('near_limit', $nearLimit->getStatusAttribute());
        $this->assertEquals('over_budget', $overBudget->getStatusAttribute());
    }

    public function test_budget_belongs_to_user(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $this->assertInstanceOf(User::class, $budget->user);
        $this->assertEquals($this->user->id, $budget->user->id);
    }

    public function test_budget_belongs_to_category(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $this->assertInstanceOf(Category::class, $budget->category);
        $this->assertEquals($this->category->id, $budget->category->id);
    }

    public function test_budget_scope_for_period(): void
    {
        $currentBudget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'year' => now()->year,
            'month' => now()->month,
        ]);

        $previousBudget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'year' => now()->subMonth()->year,
            'month' => now()->subMonth()->month,
        ]);

        $budgets = Budget::forPeriod(now()->year, now()->month)->get();

        $this->assertCount(1, $budgets);
        $this->assertEquals($currentBudget->id, $budgets->first()->id);
    }

    public function test_budget_scope_for_user(): void
    {
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);

        Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        Budget::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
        ]);

        $userBudgets = Budget::forUser($this->user->id)->get();

        $this->assertCount(1, $userBudgets);
        $this->assertEquals($this->user->id, $userBudgets->first()->user_id);
    }

    public function test_budget_scope_over_budget(): void
    {
        Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 1000.00,
            'spent_amount' => 800.00, // Under budget
        ]);

        Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 500.00,
            'spent_amount' => 600.00, // Over budget
        ]);

        $overBudgets = Budget::overBudget()->get();

        $this->assertCount(1, $overBudgets);
        $this->assertEquals(600.00, $overBudgets->first()->spent_amount);
    }

    public function test_budget_scope_near_limit(): void
    {
        Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 1000.00,
            'spent_amount' => 500.00, // 50% - not near limit
        ]);

        Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 1000.00,
            'spent_amount' => 900.00, // 90% - near limit
        ]);

        Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 500.00,
            'spent_amount' => 600.00, // Over budget - not near limit
        ]);

        $nearLimitBudgets = Budget::nearLimit()->get();

        $this->assertCount(1, $nearLimitBudgets);
        $this->assertEquals(900.00, $nearLimitBudgets->first()->spent_amount);
    }

    public function test_budget_daily_average_calculation(): void
    {
        $budget = Budget::factory()->make([
            'amount' => 3100.00, // $100 per day for 31-day month
            'year' => now()->year,
            'month' => now()->month,
        ]);

        $daysInMonth = now()->daysInMonth;
        $expectedDailyAverage = 3100.00 / $daysInMonth;

        $this->assertEquals($expectedDailyAverage, $budget->getDailyAverageAttribute());
    }

    public function test_budget_projected_spending_calculation(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 3100.00,
            'year' => now()->year,
            'month' => now()->month,
        ]);

        // Create transactions for first 10 days of month
        for ($day = 1; $day <= 10; $day++) {
            Transaction::factory()->create([
                'user_id' => $this->user->id,
                'account_id' => $this->account->id,
                'category_id' => $this->category->id,
                'type' => 'expense',
                'amount' => 10.00, // $10 per day
                'date' => now()->startOfMonth()->addDays($day - 1),
            ]);
        }

        $daysInMonth = now()->daysInMonth;
        $expectedProjection = (100.00 / 10) * $daysInMonth; // $10/day * days in month

        $this->assertEquals($expectedProjection, $budget->getProjectedSpendingAttribute());
    }
}