<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'description',
        'amount',
        'type',
        'date',
        'reference',
        'notes',
        'transfer_account_id',
        'transfer_transaction_id',
        'is_recurring',
        'recurring_frequency',
        'recurring_end_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'recurring_end_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    /**
     * Retrieve the model for a bound value.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $query = $this->where($field ?? $this->getRouteKeyName(), $value);

        // Scope by user_id if user is authenticated (including test scenarios)
        $user = auth()->user();
        if ($user) {
            $query->where('user_id', $user->id);
        }

        return $query->first();
    }

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
        return $this->belongsTo(Account::class, 'transfer_account_id');
    }

    public function transferAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'transfer_account_id');
    }

    public function transferTransaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'transfer_transaction_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
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
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeInDateRange($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
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

    // Model Events for Balance Updates - Disabled to handle manually in controller
    // protected static function booted()
    // {
    //     static::created(function ($transaction) {
    //         $transaction->updateAccountBalance();
    //     });

    //     static::updated(function ($transaction) {
    //         $transaction->updateAccountBalance();
    //     });

    //     static::deleted(function ($transaction) {
    //         $transaction->reverseAccountBalance();
    //     });
    // }

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
        // Transfers OUT: when this account creates a transfer (money going out)
        $transfersOut = $account->transactions()
            ->where('type', 'transfer')
            ->whereNotNull('transfer_account_id')
            ->sum('amount');

        // Transfers IN: when this account receives a transfer (money coming in)
        $transfersIn = Transaction::where('transfer_account_id', $account->id)
            ->where('type', 'transfer')
            ->sum('amount');

        $balance = $balance - $transfersOut + $transfersIn;

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

    /**
     * Encrypt/decrypt sensitive notes
     */
    protected function notes(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? decrypt($value) : null,
            set: fn ($value) => $value ? encrypt($value) : null,
        );
    }

    /**
     * Hide sensitive data from serialization
     */
    protected $hidden = ['notes_encrypted'];
}
