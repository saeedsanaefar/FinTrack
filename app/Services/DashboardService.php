<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Budget;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardService
{
    private const CACHE_TTL = 300; // 5 minutes

    public function getDashboardData($userId)
    {
        $cacheKey = "dashboard_data_{$userId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            return $this->generateDashboardData($userId);
        });
    }

    public function getAccountSummary($userId)
    {
        $cacheKey = "account_summary_{$userId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            $accounts = Account::where('user_id', $userId)->get();
            $totalBalance = $accounts->sum('balance');

            $accountBreakdown = $accounts->map(function ($account) use ($totalBalance) {
                return [
                    'name' => $account->name,
                    'type' => $account->type,
                    'balance' => $account->balance,
                    'percentage' => $totalBalance > 0 ? ($account->balance / $totalBalance) * 100 : 0,
                    'description' => $account->description
                ];
            });

            return [
                'totalBalance' => $totalBalance, // Changed from total_balance
                'accountBreakdown' => $accountBreakdown, // Changed from account_breakdown
                'accounts' => $accounts
            ];
        });
    }

    public function getMonthlyStats($userId)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $cacheKey = "monthly_stats_{$userId}_{$currentMonth->format('Y_m')}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId, $currentMonth) {
            // Optimized query using single query with conditional aggregation
            $stats = Transaction::where('user_id', $userId)
                ->where('date', '>=', $currentMonth)
                ->selectRaw('
                    SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as monthly_income,
                    SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as monthly_expenses,
                    COUNT(CASE WHEN type = "income" THEN 1 END) as income_count,
                    COUNT(CASE WHEN type = "expense" THEN 1 END) as expense_count
                ')
                ->first();

            $monthlyIncome = $stats->monthly_income ?? 0;
            $monthlyExpenses = $stats->monthly_expenses ?? 0;

            return [
                'monthlyIncome' => $monthlyIncome, // Changed from monthly_income
                'monthlyExpenses' => $monthlyExpenses, // Changed from monthly_expenses
                'incomeTransactionCount' => $stats->income_count ?? 0, // Changed from income_transaction_count
                'expenseTransactionCount' => $stats->expense_count ?? 0, // Changed from expense_transaction_count
                'savingsRate' => $monthlyIncome > 0 ? (($monthlyIncome - $monthlyExpenses) / $monthlyIncome) * 100 : 0 // Changed from savings_rate
            ];
        });
    }

    public function getRecentTransactions($userId, $limit = 5)
    {
        $cacheKey = "recent_transactions_{$userId}_{$limit}";

        return Cache::remember($cacheKey, 60, function () use ($userId, $limit) { // 1 minute cache for recent data
            return Transaction::where('user_id', $userId)
                ->with(['account:id,name,type', 'category:id,name,color'])
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    public function getBudgetData($userId)
    {
        $cacheKey = "budget_data_{$userId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            // Update spent amounts first
            Budget::updateAllSpentAmounts($userId);

            $budgetAlerts = Budget::getBudgetAlerts($userId, 80);
            $currentMonthBudgets = Budget::getCurrentMonthBudgets($userId);
            $activeBudgetsCount = Budget::where('user_id', $userId)
                ->where('is_active', true)
                ->count();

            return [
                'budgetAlerts' => $budgetAlerts, // Changed from budget_alerts
                'currentMonthBudgets' => $currentMonthBudgets, // Changed from current_month_budgets
                'activeBudgetsCount' => $activeBudgetsCount // Changed from active_budgets_count
            ];
        });
    }

    public function getBalanceChange($userId)
    {
        $cacheKey = "balance_change_{$userId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            $currentMonth = Carbon::now()->startOfMonth();

            $lastMonthBalance = Transaction::where('user_id', $userId)
                ->where('date', '<', $currentMonth)
                ->selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE -amount END) as balance')
                ->value('balance') ?? 0;

            $accounts = Account::where('user_id', $userId)->get();
            $totalBalance = $accounts->sum('balance');

            $balanceChange = 0;
            if ($lastMonthBalance > 0) {
                $balanceChange = (($totalBalance - $lastMonthBalance) / $lastMonthBalance) * 100;
            }

            return $balanceChange;
        });
    }

    private function generateDashboardData($userId)
    {
        $accountData = $this->getAccountSummary($userId);
        $monthlyStats = $this->getMonthlyStats($userId);
        $recentTransactions = $this->getRecentTransactions($userId);
        $budgetData = $this->getBudgetData($userId);
        $balanceChange = $this->getBalanceChange($userId);

        return array_merge(
            $accountData,
            $monthlyStats,
            $budgetData,
            [
                'recentTransactions' => $recentTransactions, // Changed from recent_transactions
                'balanceChange' => $balanceChange // Changed from balance_change
            ]
        );
    }

    public function clearUserCache($userId)
    {
        $keys = [
            "dashboard_data_{$userId}",
            "account_summary_{$userId}",
            "monthly_stats_{$userId}_" . Carbon::now()->format('Y_m'),
            "recent_transactions_{$userId}_5",
            "budget_data_{$userId}",
            "balance_change_{$userId}"
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
