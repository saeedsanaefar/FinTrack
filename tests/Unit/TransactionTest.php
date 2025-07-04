<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Account $account;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->account = Account::factory()->create(['user_id' => $this->user->id]);
        $this->category = Category::factory()->expense()->create(['user_id' => $this->user->id]);
    }

    public function test_transaction_belongs_to_user(): void
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
        ]);

        $this->assertInstanceOf(User::class, $transaction->user);
        $this->assertEquals($this->user->id, $transaction->user->id);
    }

    public function test_transaction_belongs_to_account(): void
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
        ]);

        $this->assertInstanceOf(Account::class, $transaction->account);
        $this->assertEquals($this->account->id, $transaction->account->id);
    }

    public function test_transaction_belongs_to_category(): void
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
        ]);

        $this->assertInstanceOf(Category::class, $transaction->category);
        $this->assertEquals($this->category->id, $transaction->category->id);
    }

    public function test_transaction_formatted_amount_attribute(): void
    {
        $transaction = Transaction::factory()->make([
            'amount' => 1234.56,
        ]);

        $this->assertEquals('$1,234.56', $transaction->getFormattedAmountAttribute());
    }

    public function test_transaction_is_income_attribute(): void
    {
        $incomeTransaction = Transaction::factory()->make(['type' => 'income']);
        $expenseTransaction = Transaction::factory()->make(['type' => 'expense']);
        $transferTransaction = Transaction::factory()->make(['type' => 'transfer']);

        $this->assertTrue($incomeTransaction->getIsIncomeAttribute());
        $this->assertFalse($expenseTransaction->getIsIncomeAttribute());
        $this->assertFalse($transferTransaction->getIsIncomeAttribute());
    }

    public function test_transaction_is_expense_attribute(): void
    {
        $incomeTransaction = Transaction::factory()->make(['type' => 'income']);
        $expenseTransaction = Transaction::factory()->make(['type' => 'expense']);
        $transferTransaction = Transaction::factory()->make(['type' => 'transfer']);

        $this->assertFalse($incomeTransaction->getIsExpenseAttribute());
        $this->assertTrue($expenseTransaction->getIsExpenseAttribute());
        $this->assertFalse($transferTransaction->getIsExpenseAttribute());
    }

    public function test_transaction_is_transfer_attribute(): void
    {
        $incomeTransaction = Transaction::factory()->make(['type' => 'income']);
        $expenseTransaction = Transaction::factory()->make(['type' => 'expense']);
        $transferTransaction = Transaction::factory()->make(['type' => 'transfer']);

        $this->assertFalse($incomeTransaction->getIsTransferAttribute());
        $this->assertFalse($expenseTransaction->getIsTransferAttribute());
        $this->assertTrue($transferTransaction->getIsTransferAttribute());
    }

    public function test_transaction_scope_for_user(): void
    {
        $otherUser = User::factory()->create();
        $otherAccount = Account::factory()->create(['user_id' => $otherUser->id]);
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
        ]);

        Transaction::factory()->create([
            'user_id' => $otherUser->id,
            'account_id' => $otherAccount->id,
            'category_id' => $otherCategory->id,
        ]);

        $userTransactions = Transaction::forUser($this->user->id)->get();

        $this->assertCount(1, $userTransactions);
        $this->assertEquals($this->user->id, $userTransactions->first()->user_id);
    }

    public function test_transaction_scope_by_type(): void
    {
        Transaction::factory()->income()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
        ]);

        Transaction::factory()->expense()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
        ]);

        $incomeTransactions = Transaction::byType('income')->get();
        $expenseTransactions = Transaction::byType('expense')->get();

        $this->assertCount(1, $incomeTransactions);
        $this->assertCount(1, $expenseTransactions);
        $this->assertEquals('income', $incomeTransactions->first()->type);
        $this->assertEquals('expense', $expenseTransactions->first()->type);
    }

    public function test_transaction_scope_by_account(): void
    {
        $anotherAccount = Account::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $anotherAccount->id,
            'category_id' => $this->category->id,
        ]);

        $accountTransactions = Transaction::byAccount($this->account->id)->get();

        $this->assertCount(1, $accountTransactions);
        $this->assertEquals($this->account->id, $accountTransactions->first()->account_id);
    }

    public function test_transaction_scope_by_category(): void
    {
        $anotherCategory = Category::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $anotherCategory->id,
        ]);

        $categoryTransactions = Transaction::byCategory($this->category->id)->get();

        $this->assertCount(1, $categoryTransactions);
        $this->assertEquals($this->category->id, $categoryTransactions->first()->category_id);
    }

    public function test_transaction_scope_between_dates(): void
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'date' => $startDate->addDays(5),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'date' => $startDate->subMonth(), // Previous month
        ]);

        $monthTransactions = Transaction::betweenDates(
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        )->get();

        $this->assertCount(1, $monthTransactions);
    }

    public function test_transaction_scope_this_month(): void
    {
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'date' => Carbon::now(),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'date' => Carbon::now()->subMonth(),
        ]);

        $thisMonthTransactions = Transaction::thisMonth()->get();

        $this->assertCount(1, $thisMonthTransactions);
        $this->assertEquals(Carbon::now()->month, $thisMonthTransactions->first()->date->month);
    }

    public function test_transaction_scope_this_year(): void
    {
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'date' => Carbon::now(),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'date' => Carbon::now()->subYear(),
        ]);

        $thisYearTransactions = Transaction::thisYear()->get();

        $this->assertCount(1, $thisYearTransactions);
        $this->assertEquals(Carbon::now()->year, $thisYearTransactions->first()->date->year);
    }

    public function test_transaction_scope_search(): void
    {
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'description' => 'Grocery shopping at Walmart',
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'description' => 'Gas station fill up',
        ]);

        $searchResults = Transaction::search('grocery')->get();

        $this->assertCount(1, $searchResults);
        $this->assertStringContainsString('Grocery', $searchResults->first()->description);
    }

    public function test_transaction_scope_amount_between(): void
    {
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'amount' => 50.00,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'amount' => 150.00,
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'amount' => 250.00,
        ]);

        $filteredTransactions = Transaction::amountBetween(100, 200)->get();

        $this->assertCount(1, $filteredTransactions);
        $this->assertEquals(150.00, $filteredTransactions->first()->amount);
    }

    public function test_transaction_scope_recent(): void
    {
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'date' => Carbon::now(),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'date' => Carbon::now()->subDays(5),
        ]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'date' => Carbon::now()->subDays(15),
        ]);

        $recentTransactions = Transaction::recent(10)->get();

        $this->assertCount(2, $recentTransactions);
    }

    public function test_transaction_amount_impact_calculation(): void
    {
        $incomeTransaction = Transaction::factory()->make([
            'type' => 'income',
            'amount' => 1000.00,
        ]);

        $expenseTransaction = Transaction::factory()->make([
            'type' => 'expense',
            'amount' => 500.00,
        ]);

        $transferTransaction = Transaction::factory()->make([
            'type' => 'transfer',
            'amount' => 200.00,
        ]);

        $this->assertEquals(1000.00, $incomeTransaction->getAmountImpactAttribute());
        $this->assertEquals(-500.00, $expenseTransaction->getAmountImpactAttribute());
        $this->assertEquals(0, $transferTransaction->getAmountImpactAttribute());
    }

    public function test_transaction_date_formatting(): void
    {
        $transaction = Transaction::factory()->make([
            'date' => Carbon::create(2024, 1, 15),
        ]);

        $this->assertEquals('2024-01-15', $transaction->getFormattedDateAttribute());
        $this->assertEquals('Jan 15, 2024', $transaction->getHumanDateAttribute());
    }

    public function test_transaction_is_recurring_attribute(): void
    {
        $recurringTransaction = Transaction::factory()->make([
            'recurring_transaction_id' => 1,
        ]);

        $oneTimeTransaction = Transaction::factory()->make([
            'recurring_transaction_id' => null,
        ]);

        $this->assertTrue($recurringTransaction->getIsRecurringAttribute());
        $this->assertFalse($oneTimeTransaction->getIsRecurringAttribute());
    }

    public function test_transaction_age_calculation(): void
    {
        $transaction = Transaction::factory()->make([
            'date' => Carbon::now()->subDays(5),
        ]);

        $this->assertEquals(5, $transaction->getAgeInDaysAttribute());
    }
}