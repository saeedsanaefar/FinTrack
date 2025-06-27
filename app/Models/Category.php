<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'type',
        'color',
        'icon',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Boot method to auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeIncome($query)
    {
        return $query->whereIn('type', ['income', 'both']);
    }

    public function scopeExpense($query)
    {
        return $query->whereIn('type', ['expense', 'both']);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getIsIncomeAttribute()
    {
        return in_array($this->type, ['income', 'both']);
    }

    public function getIsExpenseAttribute()
    {
        return in_array($this->type, ['expense', 'both']);
    }

    public function getTransactionCountAttribute()
    {
        return $this->transactions()->count();
    }

    public function getTotalAmountAttribute()
    {
        return $this->transactions()->sum('amount');
    }

    // Methods
    public function canBeDeleted()
    {
        return $this->transactions()->count() === 0;
    }

    public static function getDefaultCategories($userId)
    {
        return [
            // Income categories
            ['user_id' => $userId, 'name' => 'Salary', 'type' => 'income', 'color' => '#10B981', 'icon' => 'briefcase', 'sort_order' => 1],
            ['user_id' => $userId, 'name' => 'Freelance', 'type' => 'income', 'color' => '#3B82F6', 'icon' => 'laptop', 'sort_order' => 2],
            ['user_id' => $userId, 'name' => 'Investment', 'type' => 'income', 'color' => '#8B5CF6', 'icon' => 'trending-up', 'sort_order' => 3],
            ['user_id' => $userId, 'name' => 'Other Income', 'type' => 'income', 'color' => '#06B6D4', 'icon' => 'plus-circle', 'sort_order' => 4],
            
            // Expense categories
            ['user_id' => $userId, 'name' => 'Food & Dining', 'type' => 'expense', 'color' => '#EF4444', 'icon' => 'utensils', 'sort_order' => 5],
            ['user_id' => $userId, 'name' => 'Transportation', 'type' => 'expense', 'color' => '#F59E0B', 'icon' => 'car', 'sort_order' => 6],
            ['user_id' => $userId, 'name' => 'Shopping', 'type' => 'expense', 'color' => '#EC4899', 'icon' => 'shopping-bag', 'sort_order' => 7],
            ['user_id' => $userId, 'name' => 'Entertainment', 'type' => 'expense', 'color' => '#8B5CF6', 'icon' => 'film', 'sort_order' => 8],
            ['user_id' => $userId, 'name' => 'Bills & Utilities', 'type' => 'expense', 'color' => '#6B7280', 'icon' => 'receipt', 'sort_order' => 9],
            ['user_id' => $userId, 'name' => 'Healthcare', 'type' => 'expense', 'color' => '#DC2626', 'icon' => 'heart', 'sort_order' => 10],
            ['user_id' => $userId, 'name' => 'Education', 'type' => 'expense', 'color' => '#059669', 'icon' => 'academic-cap', 'sort_order' => 11],
            ['user_id' => $userId, 'name' => 'Other Expenses', 'type' => 'expense', 'color' => '#6B7280', 'icon' => 'minus-circle', 'sort_order' => 12],
        ];
    }
}
