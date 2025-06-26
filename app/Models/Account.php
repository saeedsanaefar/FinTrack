<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'balance',
        'currency',
        'description',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the account.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the account type in a human-readable format.
     */
    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'checking' => 'Checking Account',
            'savings' => 'Savings Account',
            'credit_card' => 'Credit Card',
            'cash' => 'Cash',
            'investment' => 'Investment Account',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get the formatted balance with currency.
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 2) . ' ' . $this->currency;
    }

    /**
     * Scope to get only active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
