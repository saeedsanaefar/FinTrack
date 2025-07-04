<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Budget;
use App\Models\Category;
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
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        
        $this->user = User::factory()->create();
        $this->category = Category::factory()->for($this->user)->create();
        $this->account = Account::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_user_can_create_budget(): void
    {
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post('/budgets', [
                'category_id' => $this->category->id,
                'amount' => 500.00,
                'year' => now()->year,
                'month' => now()->month,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('budgets', [
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 500.00,
        ]);
    }

    public function test_user_can_view_budgets_index(): void
    {
        Budget::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)->get('/budgets');

        $response->assertStatus(200);
        $response->assertViewIs('budgets.index');
        $response->assertViewHas('budgets');
    }

    public function test_user_can_update_budget(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 500.00,
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->put("/budgets/{$budget->id}", [
                'category_id' => $this->category->id,
                'amount' => 750.00,
                'year' => $budget->year,
                'month' => $budget->month,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('budgets', [
            'id' => $budget->id,
            'amount' => 750.00,
        ]);
    }

    public function test_user_can_delete_budget(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->delete("/budgets/{$budget->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('budgets', [
            'id' => $budget->id,
        ]);
    }

    public function test_budget_spent_amount_calculation(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 500.00,
            'year' => now()->year,
            'month' => now()->month,
        ]);

        // Create transactions for this month and category
        Transaction::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 100.00,
            'date' => now(),
        ]);

        // Refresh the budget to get updated spent_amount
        $budget->refresh();
        
        $this->assertEquals(300.00, $budget->spent_amount);
        $this->assertEquals(60, $budget->progress_percentage);
    }

    public function test_budget_progress_percentage_calculation(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 1000.00,
            'spent_amount' => 250.00,
        ]);

        $this->assertEquals(25, $budget->progress_percentage);
    }

    public function test_budget_over_limit_detection(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 500.00,
            'spent_amount' => 600.00,
        ]);

        $this->assertTrue($budget->is_over_budget);
        $this->assertEquals(120, $budget->progress_percentage);
    }

    public function test_user_cannot_access_other_users_budgets(): void
    {
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);
        $otherBudget = Budget::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
        ]);

        $response = $this->actingAs($this->user)->get("/budgets/{$otherBudget->id}");
        $response->assertStatus(404);

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->put("/budgets/{$otherBudget->id}", []);
        $response->assertStatus(404);

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->delete("/budgets/{$otherBudget->id}");
        $response->assertStatus(404);
    }

    public function test_budget_validation_rules(): void
    {
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post('/budgets', [
                'category_id' => '', // Required field
                'amount' => -100, // Should be positive
                'year' => 'invalid-year',
                'month' => 13, // Invalid month
            ]);

        $response->assertSessionHasErrors(['category_id', 'amount', 'year', 'month']);
    }

    public function test_duplicate_budget_prevention(): void
    {
        // Create first budget
        Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'year' => now()->year,
            'month' => now()->month,
        ]);

        // Try to create duplicate budget
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post('/budgets', [
                'category_id' => $this->category->id,
                'amount' => 500.00,
                'year' => now()->year,
                'month' => now()->month,
            ]);

        $response->assertSessionHasErrors(['category_id']);
    }

    public function test_budget_alerts_functionality(): void
    {
        // Create budget near limit (90%)
        $nearLimitBudget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 1000.00,
            'spent_amount' => 900.00,
        ]);

        // Create budget over limit
        $overLimitCategory = Category::factory()->expense()->create(['user_id' => $this->user->id]);
        $overLimitBudget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $overLimitCategory->id,
            'amount' => 500.00,
            'spent_amount' => 600.00,
        ]);

        $response = $this->actingAs($this->user)->get('/budgets-alerts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'near_limit',
            'over_limit',
        ]);
    }

    public function test_budget_filtering_by_period(): void
    {
        // Create budgets for different months
        Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'year' => now()->year,
            'month' => now()->month,
        ]);

        $previousMonth = now()->subMonth();
        Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'year' => $previousMonth->year,
            'month' => $previousMonth->month,
        ]);

        $response = $this->actingAs($this->user)->get('/budgets?year=' . now()->year . '&month=' . now()->month);

        $response->assertStatus(200);
        $budgets = $response->viewData('budgets');
        $this->assertCount(1, $budgets);
    }

    public function test_budget_spent_amount_updates_with_transactions(): void
    {
        $budget = Budget::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'amount' => 500.00,
            'year' => now()->year,
            'month' => now()->month,
        ]);

        $initialSpentAmount = $budget->spent_amount;

        // Create a new transaction
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 150.00,
            'date' => now(),
        ]);

        // Update spent amounts (this would typically be done via a scheduled job or observer)
        $response = $this->actingAs($this->user)->post('/budgets-update-spent');
        $response->assertStatus(200);

        $this->assertEquals($initialSpentAmount + 150.00, $budget->fresh()->spent_amount);
    }
}