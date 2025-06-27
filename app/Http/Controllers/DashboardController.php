<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function __invoke(Request $request): View
    {
        $user = auth()->user();
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // Get account summaries
        $accounts = $user->accounts()->get();
        $totalBalance = $accounts->sum('balance');
        
        // Get monthly income and expenses for current month
        $monthlyIncome = $user->transactions()
            ->where('type', 'income')
            ->where('date', '>=', $currentMonth)
            ->sum('amount');
            
        $monthlyExpenses = $user->transactions()
            ->where('type', 'expense')
            ->where('date', '>=', $currentMonth)
            ->sum('amount');
            
        // Get transaction counts for current month
        $incomeTransactionCount = $user->transactions()
            ->where('type', 'income')
            ->where('date', '>=', $currentMonth)
            ->count();
            
        $expenseTransactionCount = $user->transactions()
            ->where('type', 'expense')
            ->where('date', '>=', $currentMonth)
            ->count();
            
        // Get last month's balance for comparison
        $lastMonthBalance = $user->transactions()
            ->where('date', '<', $currentMonth)
            ->selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE -amount END) as balance')
            ->value('balance') ?? 0;
            
        // Calculate balance change percentage
        $balanceChange = 0;
        if ($lastMonthBalance > 0) {
            $balanceChange = (($totalBalance - $lastMonthBalance) / $lastMonthBalance) * 100;
        }
        
        // Get recent transactions (last 5)
        $recentTransactions = $user->transactions()
            ->with(['account', 'category'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Get account breakdown
        $accountBreakdown = $accounts->map(function ($account) {
            return [
                'name' => $account->name,
                'type' => $account->type,
                'balance' => $account->balance,
                'percentage' => 0 // Will calculate after getting total
            ];
        });
        
        // Calculate percentages for account breakdown
        if ($totalBalance > 0) {
            $accountBreakdown = $accountBreakdown->map(function ($account) use ($totalBalance) {
                $account['percentage'] = ($account['balance'] / $totalBalance) * 100;
                return $account;
            });
        }
        
        // Calculate savings rate
        $savingsRate = 0;
        if ($monthlyIncome > 0) {
            $savingsRate = (($monthlyIncome - $monthlyExpenses) / $monthlyIncome) * 100;
        }
        
        return view('dashboard', compact(
            'totalBalance',
            'monthlyIncome', 
            'monthlyExpenses',
            'incomeTransactionCount',
            'expenseTransactionCount',
            'balanceChange',
            'recentTransactions',
            'accountBreakdown',
            'savingsRate',
            'accounts'
        ));
    }
}
