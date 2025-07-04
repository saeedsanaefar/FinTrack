<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->user = User::factory()->create();
    }

    public function test_user_can_create_category(): void
    {
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post('/categories', [
                'name' => 'Groceries',
                'type' => 'expense',
                'color' => '#FF5733',
                'icon' => 'shopping-cart',
                'is_active' => true,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'user_id' => $this->user->id,
            'name' => 'Groceries',
            'type' => 'expense',
            'color' => '#FF5733',
        ]);
    }

    public function test_user_can_view_categories_index(): void
    {
        Category::factory()->count(5)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get('/categories');

        $response->assertStatus(200);
        $response->assertViewIs('categories.index');
        $response->assertViewHas('categories');
    }

    public function test_user_can_view_single_category(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get("/categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertViewIs('categories.show');
        $response->assertViewHas('category', $category);
    }

    public function test_user_can_update_category(): void
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Old Name',
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->put("/categories/{$category->id}", [
                'name' => 'Updated Name',
                'type' => $category->type,
                'color' => '#00FF00',
                'icon' => 'updated-icon',
                'is_active' => true,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'color' => '#00FF00',
        ]);
    }

    public function test_user_can_delete_category_without_transactions(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->delete("/categories/{$category->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_user_cannot_delete_category_with_transactions(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->delete("/categories/{$category->id}");

        $response->assertRedirect();
        $response->assertSessionHasErrors(['category']);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_user_cannot_access_other_users_categories(): void
    {
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get("/categories/{$otherCategory->id}");
        $response->assertStatus(404);

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->put("/categories/{$otherCategory->id}", []);
        $response->assertStatus(404);

        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->delete("/categories/{$otherCategory->id}");
        $response->assertStatus(404);
    }

    public function test_category_validation_rules(): void
    {
        $response = $this->withoutMiddleware()
            ->actingAs($this->user)
            ->post('/categories', [
                'name' => '', // Required field
                'type' => 'invalid-type', // Should be income, expense, or transfer
                'color' => 'invalid-color', // Should be valid hex color
            ]);

        $response->assertSessionHasErrors(['name', 'type', 'color']);
    }

    public function test_category_name_uniqueness_per_user(): void
    {
        Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Groceries',
        ]);

        $response = $this->actingAs($this->user)->post('/categories', [
            'name' => 'Groceries', // Duplicate name
            'type' => 'expense',
            'color' => '#FF5733',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_different_users_can_have_same_category_name(): void
    {
        $otherUser = User::factory()->create();
        
        Category::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Groceries',
        ]);

        $response = $this->actingAs($this->user)->post('/categories', [
            'name' => 'Groceries', // Same name as other user's category
            'type' => 'expense',
            'color' => '#FF5733',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'user_id' => $this->user->id,
            'name' => 'Groceries',
        ]);
    }

    public function test_category_filtering_by_type(): void
    {
        Category::factory()->income()->count(2)->create(['user_id' => $this->user->id]);
        Category::factory()->expense()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get('/categories?type=income');

        $response->assertStatus(200);
        $categories = $response->viewData('categories');
        $this->assertCount(2, $categories);
        
        foreach ($categories as $category) {
            $this->assertEquals('income', $category->type);
        }
    }

    public function test_category_filtering_by_active_status(): void
    {
        Category::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);
        
        Category::factory()->inactive()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get('/categories?active=1');

        $response->assertStatus(200);
        $categories = $response->viewData('categories');
        $this->assertCount(2, $categories);
        
        foreach ($categories as $category) {
            $this->assertTrue($category->is_active);
        }
    }

    public function test_category_search_functionality(): void
    {
        Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Grocery Shopping',
        ]);
        
        Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Entertainment',
        ]);

        $response = $this->actingAs($this->user)->get('/categories?search=grocery');

        $response->assertStatus(200);
        $categories = $response->viewData('categories');
        $this->assertCount(1, $categories);
        $this->assertEquals('Grocery Shopping', $categories->first()->name);
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

        $response = $this->actingAs($this->user)->get("/categories/{$category->id}");

        $response->assertStatus(200);
        $categoryData = $response->viewData('category');
        $this->assertEquals(5, $categoryData->transactions_count);
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

        $response = $this->actingAs($this->user)->get("/categories/{$category->id}");

        $response->assertStatus(200);
        $categoryData = $response->viewData('category');
        $this->assertEquals(300.00, $categoryData->total_amount);
    }

    public function test_category_deactivation_instead_of_deletion(): void
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);
        
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($this->user)->patch("/categories/{$category->id}/deactivate");

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'is_active' => false,
        ]);
    }

    public function test_category_reactivation(): void
    {
        $category = Category::factory()->inactive()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->patch("/categories/{$category->id}/activate");

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'is_active' => true,
        ]);
    }
}