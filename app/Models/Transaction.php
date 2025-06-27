<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'description',
        'amount',
        'type',
        'transaction_date',
        'reference',
        'notes',
        'to_account_id',
        'transfer_transaction_id',
        'is_recurring',
        'recurring_frequency',
        'recurring_end_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'recurring_end_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function transferTransaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'transfer_transaction_id');
    }

    // Scopes
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeTransfer($query)
    {
        return $query->where('type', 'transfer');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeInDateRange($query, $start, $end)
    {
        return $query->whereBetween('transaction_date', [$start, $end]);
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getIsIncomeAttribute()
    {
        return $this->type === 'income';
    }

    public function getIsExpenseAttribute()
    {
        return $this->type === 'expense';
    }

    public function getIsTransferAttribute()
    {
        return $this->type === 'transfer';
    }

    // Model Events for Balance Updates
    protected static function booted()
    {
        static::created(function ($transaction) {
            $transaction->updateAccountBalance();
        });

        static::updated(function ($transaction) {
            $transaction->updateAccountBalance();
        });

        static::deleted(function ($transaction) {
            $transaction->reverseAccountBalance();
        });
    }

    /**
     * Update account balance based on transaction
     */
    public function updateAccountBalance()
    {
        $account = $this->account;
        
        // Calculate total balance for this account
        $balance = $account->transactions()
            ->where('type', 'income')->sum('amount') -
            $account->transactions()
            ->where('type', 'expense')->sum('amount');
            
        // Handle transfers
        $transfersIn = $account->transactions()
            ->where('type', 'transfer')
            ->whereNotNull('transfer_account_id')
            ->sum('amount');
            
        $transfersOut = Transaction::where('transfer_account_id', $account->id)
            ->where('type', 'transfer')
            ->sum('amount');
            
        $balance = $balance + $transfersIn - $transfersOut;
        
        $account->update(['balance' => $balance]);
    }

    /**
     * Reverse account balance when transaction is deleted
     */
    public function reverseAccountBalance()
    {
        $account = $this->account;
        
        if ($this->type === 'income') {
            $account->decrement('balance', $this->amount);
        } elseif ($this->type === 'expense') {
            $account->increment('balance', $this->amount);
        } elseif ($this->type === 'transfer' && $this->transfer_account_id) {
            $transferAccount = Account::find($this->transfer_account_id);
            if ($transferAccount) {
                $account->increment('balance', $this->amount);
                $transferAccount->decrement('balance', $this->amount);
            }
        }
    }
}
