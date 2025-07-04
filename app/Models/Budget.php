<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Budget extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'period_type',
        'year',
        'month',
        'spent_amount',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'year' => 'integer',
        'month' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForPeriod($query, $year, $month = null)
    {
        $query->where('year', $year);
        
        if ($month !== null) {
            $query->where('month', $month);
        }
        
        return $query;
    }

    public function scopeMonthly($query)
    {
        return $query->where('period_type', 'monthly');
    }

    public function scopeYearly($query)
    {
        return $query->where('period_type', 'yearly');
    }

    // Accessors
    public function getProgressPercentageAttribute()
    {
        if ($this->amount <= 0) {
            return 0;
        }
        
        return min(100, round(($this->spent_amount / $this->amount) * 100, 1));
    }

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->amount - $this->spent_amount);
    }

    public function getOverBudgetAmountAttribute()
    {
        return max(0, $this->spent_amount - $this->amount);
    }

    public function getStatusAttribute()
    {
        $percentage = $this->progress_percentage;
        
        if ($percentage >= 100) {
            return 'over';
        } elseif ($percentage >= 80) {
            return 'warning';
        } elseif ($percentage >= 50) {
            return 'caution';
        } else {
            return 'good';
        }
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'over' => 'red',
            'warning' => 'orange',
            'caution' => 'yellow',
            'good' => 'green',
            default => 'gray'
        };
    }

    public function getPeriodLabelAttribute()
    {
        if ($this->period_type === 'yearly') {
            return $this->year;
        }
        
        return Carbon::createFromDate($this->year, $this->month, 1)->format('M Y');
    }

    // Methods
    public function isOverBudget()
    {
        return $this->spent_amount > $this->amount;
    }

    public function isNearLimit($threshold = 80)
    {
        return $this->progress_percentage >= $threshold;
    }

    public function updateSpentAmount()
    {
        $spent = $this->calculateSpentAmount();
        $this->update(['spent_amount' => $spent]);
        return $spent;
    }

    public function calculateSpentAmount()
    {
        $query = Transaction::where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('type', 'expense');

        if ($this->period_type === 'monthly') {
            $query->whereYear('date', $this->year)
                  ->whereMonth('date', $this->month);
        } else {
            $query->whereYear('date', $this->year);
        }

        return $query->sum('amount') ?? 0;
    }

    public static function updateAllSpentAmounts($userId = null)
    {
        $query = self::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        $budgets = $query->get();
        
        foreach ($budgets as $budget) {
            $budget->updateSpentAmount();
        }
        
        return $budgets->count();
    }

    public static function getBudgetAlerts($userId, $threshold = 80)
    {
        return self::forUser($userId)
            ->active()
            ->with('category')
            ->get()
            ->filter(function ($budget) use ($threshold) {
                return $budget->progress_percentage >= $threshold;
            })
            ->sortByDesc('progress_percentage');
    }

    public static function getCurrentMonthBudgets($userId)
    {
        $now = Carbon::now();
        
        return self::forUser($userId)
            ->active()
            ->monthly()
            ->forPeriod($now->year, $now->month)
            ->with('category')
            ->orderBy('spent_amount', 'desc')
            ->get();
    }

    public static function getCurrentYearBudgets($userId)
    {
        $now = Carbon::now();
        
        return self::forUser($userId)
            ->active()
            ->yearly()
            ->forPeriod($now->year)
            ->with('category')
            ->orderBy('spent_amount', 'desc')
            ->get();
    }
}