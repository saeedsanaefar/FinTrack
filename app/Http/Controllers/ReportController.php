<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->endOfMonth();

        $user = auth()->user();

        // Income vs Expense by month
        $monthlyData = $user->transactions()
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month, type, SUM(amount) as total')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('month', 'type')
            ->orderBy('month')
            ->get()
            ->groupBy('month');

        // Category breakdown for expenses
        $categoryData = $user->transactions()
            ->with('category')
            ->selectRaw('category_id, SUM(amount) as total')
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('category_id')
            ->having('total', '>', 0)
            ->get();

        // Account balances over time
        $accountData = $user->accounts()->with([
            'transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
        ])->get();

        // Summary statistics
        $totalIncome = $user->transactions()
            ->where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $totalExpenses = $user->transactions()
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $netIncome = $totalIncome - $totalExpenses;

        // Top spending categories
        $topCategories = $user->transactions()
            ->with('category')
            ->selectRaw('category_id, SUM(amount) as total')
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Recent transactions
        $recentTransactions = $user->transactions()
            ->with(['account', 'category'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        return view('reports.index', compact(
            'monthlyData', 'categoryData', 'accountData', 'startDate', 'endDate',
            'totalIncome', 'totalExpenses', 'netIncome', 'topCategories', 'recentTransactions'
        ));
    }

    public function totalIncome()
    {
        $totalIncome = DB::table('transactions')
            ->where('user_id', auth()->id())
            ->where('type', 'income')
            ->sum('amount');

        return response()->json(['total' => $totalIncome]);
    }

    public function exportCsv(Request $request)
    {
        $query = auth()->user()->transactions()
            ->with(['account', 'category']);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions.csv"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Description', 'Category', 'Account', 'Type', 'Amount']);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->date,
                    $transaction->description,
                    $transaction->category->name,
                    $transaction->account->name,
                    $transaction->type,
                    $transaction->amount,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getChartData(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->endOfMonth();

        $user = auth()->user();

        // Monthly data for line chart
        $monthlyData = $user->transactions()
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month, type, SUM(amount) as total')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('month', 'type')
            ->orderBy('month')
            ->get()
            ->groupBy('month');

        $months = [];
        $incomeData = [];
        $expenseData = [];

        foreach ($monthlyData as $month => $data) {
            $months[] = Carbon::createFromFormat('Y-m', $month)->format('M Y');
            $incomeData[] = $data->where('type', 'income')->sum('total') ?: 0;
            $expenseData[] = $data->where('type', 'expense')->sum('total') ?: 0;
        }

        // Category data for pie chart
        $categoryData = $user->transactions()
            ->with('category')
            ->selectRaw('category_id, SUM(amount) as total')
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('category_id')
            ->having('total', '>', 0)
            ->get();

        $categoryLabels = $categoryData->pluck('category.name')->toArray();
        $categoryAmounts = $categoryData->pluck('total')->toArray();
        $categoryColors = $categoryData->map(function($item) {
            return $item->category->color ?? '#' . substr(md5($item->category->name), 0, 6);
        })->toArray();

        return response()->json([
            'monthly' => [
                'labels' => $months,
                'income' => $incomeData,
                'expenses' => $expenseData,
            ],
            'categories' => [
                'labels' => $categoryLabels,
                'data' => $categoryAmounts,
                'colors' => $categoryColors,
            ]
        ]);
    }
}
