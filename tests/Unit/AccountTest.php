<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    public function test_account_belongs_to_user(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        $this->assertInstanceOf(User::class, $account->user);
        $this->assertEquals($this->user->id, $account->user->id);
    }

    public function test_account_has_many_transactions(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        
        Transaction::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
        ]);

        $this->assertCount(3, $account->transactions);
        $this->assertInstanceOf(Transaction::class, $account->transactions->first());
    }

    public function test_account_formatted_balance_attribute(): void
    {
        $account = Account::factory()->make(['balance' => 1234.56]);

        $this->assertEquals('$1,234.56', $account->getFormattedBalanceAttribute());
    }

    public function test_account_is_active_attribute(): void
    {
        $activeAccount = Account::factory()->make(['is_active' => true]);
        $inactiveAccount = Account::factory()->make(['is_active' => false]);

        $this->assertTrue($activeAccount->getIsActiveAttribute());
        $this->assertFalse($inactiveAccount->getIsActiveAttribute());
    }

    public function test_account_is_credit_card_attribute(): void
    {
        $creditCardAccount = Account::factory()->make(['type' => 'credit_card']);
        $checkingAccount = Account::factory()->make(['type' => 'checking']);

        $this->assertTrue($creditCardAccount->getIsCreditCardAttribute());
        $this->assertFalse($checkingAccount->getIsCreditCardAttribute());
    }

    public function test_account_balance_calculation_with_transactions(): void
    {
        $account = Account::factory()->create([
            'user_id' => $this->user->id,
            'balance' => 1000.00,
        ]);
        
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        // Add income transaction
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 500.00,
        ]);

        // Add expense transaction
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 200.00,
        ]);

        $calculatedBalance = $account->calculateBalance();
        $this->assertEquals(1300.00, $calculatedBalance); // 1000 + 500 - 200
    }

    public function test_account_scope_active(): void
    {
        Account::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        Account::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => false,
        ]);

        $activeAccounts = Account::active()->get();

        $this->assertCount(1, $activeAccounts);
        $this->assertTrue($activeAccounts->first()->is_active);
    }

    public function test_account_scope_by_type(): void
    {
        Account::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'checking',
        ]);

        Account::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'savings',
        ]);

        Account::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'credit_card',
        ]);

        $checkingAccounts = Account::byType('checking')->get();
        $creditCardAccounts = Account::byType('credit_card')->get();

        $this->assertCount(1, $checkingAccounts);
        $this->assertCount(1, $creditCardAccounts);
        $this->assertEquals('checking', $checkingAccounts->first()->type);
        $this->assertEquals('credit_card', $creditCardAccounts->first()->type);
    }

    public function test_account_scope_for_user(): void
    {
        $otherUser = User::factory()->create();

        Account::factory()->create(['user_id' => $this->user->id]);
        Account::factory()->create(['user_id' => $otherUser->id]);

        $userAccounts = Account::forUser($this->user->id)->get();

        $this->assertCount(1, $userAccounts);
        $this->assertEquals($this->user->id, $userAccounts->first()->user_id);
    }

    public function test_account_total_income_calculation(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 100.00,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 50.00,
        ]);

        $this->assertEquals(300.00, $account->getTotalIncomeAttribute());
    }

    public function test_account_total_expenses_calculation(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 150.00,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 200.00,
        ]);

        $this->assertEquals(300.00, $account->getTotalExpensesAttribute());
    }

    public function test_account_transaction_count(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
        ]);

        $this->assertEquals(5, $account->getTransactionCountAttribute());
    }

    public function test_account_last_transaction_date(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        $category = Category::factory()->create(['user_id' => $this->user->id]);

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
            $account->getLastTransactionDateAttribute()->format('Y-m-d')
        );
    }

    public function test_account_currency_formatting(): void
    {
        $usdAccount = Account::factory()->make([
            'currency' => 'USD',
            'balance' => 1000.00,
        ]);

        $eurAccount = Account::factory()->make([
            'currency' => 'EUR',
            'balance' => 1000.00,
        ]);

        $this->assertStringContainsString('$', $usdAccount->getFormattedBalanceAttribute());
        $this->assertStringContainsString('€', $eurAccount->getFormattedBalanceAttribute());
    }

    public function test_account_net_worth_calculation(): void
    {
        $account = Account::factory()->create([
            'user_id' => $this->user->id,
            'balance' => 1000.00,
            'type' => 'checking',
        ]);

        $creditCardAccount = Account::factory()->create([
            'user_id' => $this->user->id,
            'balance' => -500.00, // Credit card debt
            'type' => 'credit_card',
        ]);

        $netWorth = Account::forUser($this->user->id)->sum('balance');
        $this->assertEquals(500.00, $netWorth); // 1000 - 500
    }

    public function test_account_monthly_summary(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        // Current month transactions
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 2000.00,
            'date' => Carbon::now(),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 800.00,
            'date' => Carbon::now(),
        ]);

        // Previous month transaction (should not be included)
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 1000.00,
            'date' => Carbon::now()->subMonth(),
        ]);

        $monthlyIncome = $account->getMonthlyIncomeAttribute();
        $monthlyExpenses = $account->getMonthlyExpensesAttribute();

        $this->assertEquals(2000.00, $monthlyIncome);
        $this->assertEquals(800.00, $monthlyExpenses);
    }

    public function test_account_can_be_deleted_when_no_transactions(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        $this->assertTrue($account->canBeDeleted());
    }

    public function test_account_cannot_be_deleted_when_has_transactions(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
        ]);

        $this->assertFalse($account->canBeDeleted());
    }

    public function test_account_deactivation(): void
    {
        $account = Account::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        $account->deactivate();

        $this->assertFalse($account->is_active);
    }

    public function test_account_reactivation(): void
    {
        $account = Account::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => false,
        ]);

        $account->activate();

        $this->assertTrue($account->is_active);
    }
}