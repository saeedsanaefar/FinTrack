<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'to_account_id',
        'type',
        'amount',
        'description',
        'reference',
        'notes',
        'frequency',
        'start_date',
        'end_date',
        'next_due_date',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_due_date' => 'date',
        'is_active' => 'boolean',
    ];

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

    public function generateTransaction(): Transaction
    {
        $transaction = Transaction::create([
            'user_id' => $this->user_id,
            'account_id' => $this->account_id,
            'category_id' => $this->category_id,
            'to_account_id' => $this->to_account_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'reference' => $this->reference,
            'notes' => $this->notes,
            'date' => $this->next_due_date,
            'is_recurring' => false, // Generated transactions are not recurring themselves
        ]);

        // Update next due date
        $this->updateNextDueDate();

        return $transaction;
    }

    public function updateNextDueDate(): void
    {
        $nextDate = match ($this->frequency) {
            'daily' => $this->next_due_date->addDay(),
            'weekly' => $this->next_due_date->addWeek(),
            'monthly' => $this->next_due_date->addMonth(),
            'quarterly' => $this->next_due_date->addMonths(3),
            'yearly' => $this->next_due_date->addYear(),
            default => $this->next_due_date->addMonth(),
        };

        // Check if we've passed the end date
        if ($this->end_date && $nextDate->gt($this->end_date)) {
            $this->is_active = false;
        }

        $this->next_due_date = $nextDate;
        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDue($query)
    {
        return $query->where('next_due_date', '<=', now()->toDateString());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
