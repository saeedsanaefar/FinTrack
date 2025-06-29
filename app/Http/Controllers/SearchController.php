<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;

class SearchController extends Controller
{
    public function transactions(Request $request)
    {
        $query = auth()->user()->transactions()->with(['account', 'category']);

        // Text search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('notes', 'like', '%' . $request->search . '%');
            });
        }

        // Date range
        if ($request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        // Amount range
        if ($request->min_amount) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->max_amount) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Account and category filters
        if ($request->account_id) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Transaction type filter
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Quick filters
        if ($request->quick_filter) {
            switch ($request->quick_filter) {
                case 'this_month':
                    $query->whereMonth('transaction_date', now()->month)
                          ->whereYear('transaction_date', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('transaction_date', now()->subMonth()->month)
                          ->whereYear('transaction_date', now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('transaction_date', now()->year);
                    break;
                case 'income_only':
                    $query->where('type', 'income');
                    break;
                case 'expenses_only':
                    $query->where('type', 'expense');
                    break;
                case 'under_50':
                    $query->where('amount', '<', 50);
                    break;
                case '50_to_200':
                    $query->whereBetween('amount', [50, 200]);
                    break;
                case 'over_200':
                    $query->where('amount', '>', 200);
                    break;
            }
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        // Get data for filter dropdowns
        $accounts = auth()->user()->accounts;
        $categories = auth()->user()->categories;

        return view('transactions.index', compact('transactions', 'accounts', 'categories'));
    }

    public function api(Request $request)
    {
        $query = auth()->user()->transactions()->with(['account', 'category']);

        // Apply same filters as transactions method
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('notes', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        if ($request->min_amount) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->max_amount) {
            $query->where('amount', '<=', $request->max_amount);
        }

        if ($request->account_id) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        return response()->json([
            'transactions' => $transactions->items(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ]
        ]);
    }
}
