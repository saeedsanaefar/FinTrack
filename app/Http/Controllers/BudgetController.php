<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Http\Requests\CreateBudgetRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class BudgetController extends Controller
{
    /**
     * Display a listing of the budgets.
     */
    public function index(Request $request): View
    {
        $query = Budget::where('user_id', auth()->id())
            ->with(['category'])
            ->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('period_type')) {
            $query->where('period_type', $request->period_type);
        }
        
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('category', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $budgets = $query->paginate(20)->withQueryString();
        
        // Update spent amounts for all budgets
        foreach ($budgets as $budget) {
            $budget->updateSpentAmount();
        }
        
        // Get filter options
        $categories = Category::where('user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $years = range(Carbon::now()->year - 2, Carbon::now()->year + 2);
        
        return view('budgets.index', compact('budgets', 'categories', 'years'));
    }

    /**
     * Show the form for creating a new budget.
     */
    public function create(): View
    {
        $categories = Category::where('user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $years = range(Carbon::now()->year, Carbon::now()->year + 2);
        
        return view('budgets.create', compact('categories', 'years'));
    }

    /**
     * Store a newly created budget in storage.
     */
    public function store(CreateBudgetRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        
        $budget = Budget::create($validated);
        
        // Calculate initial spent amount
        $budget->updateSpentAmount();
        
        return redirect()->route('budgets.index')
            ->with('success', 'Budget created successfully.');
    }

    /**
     * Display the specified budget.
     */
    public function show(Budget $budget): View
    {
        $this->authorize('view', $budget);
        
        $budget->load(['category']);
        $budget->updateSpentAmount();
        
        // Get recent transactions for this budget
        $recentTransactions = \App\Models\Transaction::where('user_id', auth()->id())
            ->where('category_id', $budget->category_id)
            ->where('type', 'expense')
            ->when($budget->period_type === 'monthly', function($query) use ($budget) {
                return $query->whereYear('date', $budget->year)
                            ->whereMonth('date', $budget->month);
            })
            ->when($budget->period_type === 'yearly', function($query) use ($budget) {
                return $query->whereYear('date', $budget->year);
            })
            ->with(['account'])
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
        
        return view('budgets.show', compact('budget', 'recentTransactions'));
    }

    /**
     * Show the form for editing the specified budget.
     */
    public function edit(Budget $budget): View
    {
        $this->authorize('update', $budget);
        
        $categories = Category::where('user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $years = range(Carbon::now()->year - 2, Carbon::now()->year + 2);
        
        return view('budgets.edit', compact('budget', 'categories', 'years'));
    }

    /**
     * Update the specified budget in storage.
     */
    public function update(CreateBudgetRequest $request, Budget $budget): RedirectResponse
    {
        $this->authorize('update', $budget);
        
        $validated = $request->validated();
        $budget->update($validated);
        
        // Recalculate spent amount
        $budget->updateSpentAmount();
        
        return redirect()->route('budgets.index')
            ->with('success', 'Budget updated successfully.');
    }

    /**
     * Remove the specified budget from storage.
     */
    public function destroy(Budget $budget): RedirectResponse
    {
        $this->authorize('delete', $budget);
        
        $budget->delete();
        
        return redirect()->route('budgets.index')
            ->with('success', 'Budget deleted successfully.');
    }

    /**
     * Get budget alerts for the current user.
     */
    public function alerts(): View
    {
        $alerts = Budget::getBudgetAlerts(auth()->id(), 80);
        
        return view('budgets.alerts', compact('alerts'));
    }

    /**
     * Update spent amounts for all user budgets.
     */
    public function updateSpentAmounts(): RedirectResponse
    {
        $count = Budget::updateAllSpentAmounts(auth()->id());
        
        return back()->with('success', "Updated {$count} budget(s) successfully.");
    }
}