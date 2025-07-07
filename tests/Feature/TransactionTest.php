<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Account $account;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        
        $this->user = User::factory()->create();
        $this->account = Account::factory()->create(['user_id' => $this->user->id, 'balance' => 1000.00]);
        $this->category = Category::factory()->expense()->create(['user_id' => $this->user->id]);
    }

    public function test_user_can_create_transaction(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/transactions', [
                'description' => 'Test Transaction',
                'account_id' => $this->account->id,
                'category_id' => $this->category->id,
                'type' => 'expense',
                'amount' => 100.00,
                'date' => today()->format('Y-m-d'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'description' => 'Test Transaction',
            'user_id' => $this->user->id,
            'amount' => 100.00,
        ]);
    }

    public function test_account_balance_updates_when_expense_transaction_created(): void
    {
        $initialBalance = $this->account->balance;
        
        $this->actingAs($this->user)
            ->post('/transactions', [
                'description' => 'Expense Transaction',
                'account_id' => $this->account->id,
                'category_id' => $this->category->id,
                'type' => 'expense',
                'amount' => 100.00,
                'date' => today()->format('Y-m-d'),
            ]);

        $this->assertEquals($initialBalance - 100.00, $this->account->fresh()->balance);
    }

    public function test_account_balance_updates_when_income_transaction_created(): void
    {
        $incomeCategory = Category::factory()->income()->create(['user_id' => $this->user->id]);
        $initialBalance = $this->account->balance;
        
        $this->actingAs($this->user)
            ->post('/transactions', [
                'type' => 'income',
                'amount' => 500.00,
                'description' => 'Test income transaction',
                'account_id' => $this->account->id,
                'category_id' => $incomeCategory->id,
                'date' => now()->format('Y-m-d'),
            ]);

        $this->assertEquals($initialBalance + 500.00, $this->account->fresh()->balance);
    }

    public function test_transfer_transaction_updates_both_accounts(): void
    {
        // Create fresh accounts for this test to avoid interference from previous tests
        $fromAccount = Account::factory()->create(['user_id' => $this->user->id, 'balance' => 1000.00]);
        $toAccount = Account::factory()->create(['user_id' => $this->user->id, 'balance' => 500.00]);
        $fromInitialBalance = $fromAccount->balance;
        $toInitialBalance = $toAccount->balance;
        
        // Debug: Log initial balances
        \Log::info('Test start - From account balance: ' . $fromInitialBalance);
        \Log::info('Test start - To account balance: ' . $toInitialBalance);
        
        $response = $this->actingAs($this->user)
            ->post('/transactions', [
                'description' => 'Transfer Transaction',
                'account_id' => $fromAccount->id,
                'transfer_account_id' => $toAccount->id,
                'category_id' => $this->category->id,
                'type' => 'transfer',
                'amount' => 150.00,
                'date' => today()->format('Y-m-d'),
            ]);
            
        // Debug response
        if ($response->getStatusCode() !== 302) {
            $content = $response->getContent();
            $this->fail("Request failed with status " . $response->getStatusCode() . ": " . $content);
        }

        // Check if transaction was created
        $transactionCount = Transaction::count();
        $this->assertGreaterThan(0, $transactionCount, "No transactions were created");
        
        $actualFromBalance = $fromAccount->fresh()->balance;
        $actualToBalance = $toAccount->fresh()->balance;
        
        $this->assertNotEquals(0, $actualFromBalance, "From account balance should not be 0");
        $this->assertNotEquals(500, $actualToBalance, "To account balance should have changed from initial 500");
        
        $this->assertEquals($fromInitialBalance - 150.00, $actualFromBalance);
        $this->assertEquals($toInitialBalance + 150.00, $actualToBalance);
    }

    public function test_user_can_view_transactions_index(): void
    {
        Transaction::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
        ]);

        $response = $this->actingAs($this->user)->get('/transactions');

        $response->assertStatus(200);
        $response->assertViewIs('transactions.index');
        $response->assertViewHas('transactions');
    }

    public function test_user_can_update_transaction(): void
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'amount' => 100.00,
        ]);

        $response = $this->actingAs($this->user)
            ->put("/transactions/{$transaction->id}", [
                'description' => 'Updated Transaction',
                'account_id' => $this->account->id,
                'category_id' => $this->category->id,
                'type' => $transaction->type,
                'amount' => 150.00,
                'date' => $transaction->date->format('Y-m-d'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'description' => 'Updated Transaction',
            'amount' => 150.00,
        ]);
    }

    public function test_user_can_delete_transaction(): void
    {
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/transactions/{$transaction->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('transactions', [
            'id' => $transaction->id,
        ]);
    }

    public function test_user_cannot_access_other_users_transactions(): void
    {
        $otherUser = User::factory()->create();
        $otherAccount = Account::factory()->create(['user_id' => $otherUser->id]);
        $otherTransaction = Transaction::factory()->create([
            'user_id' => $otherUser->id,
            'account_id' => $otherAccount->id,
        ]);

        $response = $this->actingAs($this->user)->get("/transactions/{$otherTransaction->id}");
        $response->assertStatus(404);

        $response = $this->actingAs($this->user)
            ->put("/transactions/{$otherTransaction->id}", [
                'account_id' => $this->account->id,
                'category_id' => $this->category->id,
                'description' => 'Test transaction',
                'amount' => 100.00,
                'type' => 'expense',
                'date' => '2023-01-01'
            ]);
        $response->assertStatus(404);

        $response = $this->actingAs($this->user)
            ->delete("/transactions/{$otherTransaction->id}");
        $response->assertStatus(404);
    }

    public function test_transaction_validation_rules(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/transactions', [
                'description' => '', // Required field
                'amount' => -50, // Should be positive
                'date' => 'invalid-date',
            ]);

        $response->assertSessionHasErrors(['description', 'amount', 'date', 'account_id', 'type']);
    }

    public function test_transaction_filtering_by_account(): void
    {
        $anotherAccount = Account::factory()->create(['user_id' => $this->user->id]);
        
        Transaction::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
        ]);
        
        Transaction::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'account_id' => $anotherAccount->id,
        ]);

        $response = $this->actingAs($this->user)->get('/transactions?account_id=' . $this->account->id);

        $response->assertStatus(200);
        $transactions = $response->viewData('transactions');
        $this->assertCount(3, $transactions);
    }

    public function test_transaction_search_functionality(): void
    {
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'description' => 'Grocery Shopping',
        ]);
        
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'description' => 'Gas Station',
        ]);

        $response = $this->actingAs($this->user)->get('/transactions?search=Grocery');

        $response->assertStatus(200);
        $response->assertSee('Grocery Shopping');
        $response->assertDontSee('Gas Station');
    }
}