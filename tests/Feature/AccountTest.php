<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->user = User::factory()->create();
    }

    public function test_user_can_create_account(): void
    {
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post('/accounts', [
                'name' => 'Test Account',
                'type' => 'checking',
                'balance' => 1000.00,
                'currency' => 'USD',
                'description' => 'Test account description',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('accounts', [
            'name' => 'Test Account',
            'user_id' => $this->user->id,
            'balance' => 1000.00,
        ]);
    }

    public function test_user_can_view_accounts_index(): void
    {
        Account::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get('/accounts');

        $response->assertStatus(200);
        $response->assertViewIs('accounts.index');
        $response->assertViewHas('accounts');
    }

    public function test_user_can_view_single_account(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get("/accounts/{$account->id}");

        $response->assertStatus(200);
        $response->assertViewIs('accounts.show');
        $response->assertViewHas('account');
        $response->assertSee($account->name);
    }

    public function test_user_can_update_account(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->put("/accounts/{$account->id}", [
                'name' => 'Updated Account Name',
                'type' => $account->type,
                'balance' => $account->balance,
                'currency' => $account->currency,
                'description' => 'Updated description',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'name' => 'Updated Account Name',
            'description' => 'Updated description',
        ]);
    }

    public function test_user_can_delete_account(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->delete("/accounts/{$account->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('accounts', [
            'id' => $account->id,
        ]);
    }

    public function test_user_cannot_access_other_users_accounts(): void
    {
        $otherUser = User::factory()->create();
        $otherAccount = Account::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get("/accounts/{$otherAccount->id}");
        $response->assertStatus(404);

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->put("/accounts/{$otherAccount->id}", []);
        $response->assertStatus(404);

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->delete("/accounts/{$otherAccount->id}");
        $response->assertStatus(404);
    }

    public function test_account_validation_rules(): void
    {
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post('/accounts', [
                'name' => '', // Required field
                'type' => 'invalid-type',
                'balance' => 'not-a-number',
                'currency' => '',
            ]);

        $response->assertSessionHasErrors(['name', 'type', 'balance', 'currency']);
    }

    public function test_account_balance_calculation_with_transactions(): void
    {
        $account = Account::factory()->create([
            'user_id' => $this->user->id,
            'balance' => 1000.00
        ]);

        // Create some transactions
        Transaction::factory()->income()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'amount' => 500.00,
        ]);

        Transaction::factory()->expense()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'amount' => 200.00,
        ]);

        // The balance should be updated by the transaction creation logic
        $expectedBalance = 1000.00 + 500.00 - 200.00;
        $this->assertEquals($expectedBalance, $account->fresh()->balance);
    }

    public function test_inactive_accounts_are_filtered(): void
    {
        Account::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
            'name' => 'Active Account'
        ]);
        
        Account::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => false,
            'name' => 'Inactive Account'
        ]);

        $response = $this->actingAs($this->user)->get('/accounts');

        $response->assertStatus(200);
        $response->assertSee('Active Account');
        $response->assertDontSee('Inactive Account');
    }

    public function test_account_types_are_properly_handled(): void
    {
        $types = ['checking', 'savings', 'credit', 'cash', 'investment'];
        
        foreach ($types as $type) {
            $response = $this->withoutMiddleware()
                ->actingAs($this->user)
                ->post('/accounts', [
                    'name' => ucfirst($type) . ' Account',
                    'type' => $type,
                    'balance' => 1000.00,
                    'currency' => 'USD',
                ]);

            $response->assertRedirect();
            $this->assertDatabaseHas('accounts', [
                'name' => ucfirst($type) . ' Account',
                'type' => $type,
                'user_id' => $this->user->id,
            ]);
        }
    }

    public function test_account_currency_validation(): void
    {
        $validCurrencies = ['USD', 'EUR', 'GBP', 'JPY'];
        
        foreach ($validCurrencies as $currency) {
            $response = $this->withoutMiddleware()
                ->actingAs($this->user)
                ->post('/accounts', [
                    'name' => $currency . ' Account',
                    'type' => 'checking',
                    'balance' => 1000.00,
                    'currency' => $currency,
                ]);

            $response->assertRedirect();
            $this->assertDatabaseHas('accounts', [
                'currency' => $currency,
                'user_id' => $this->user->id,
            ]);
        }
    }
}