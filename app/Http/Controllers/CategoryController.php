<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CreateCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    // Authentication is handled via route middleware in web.php

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Category::where('user_id', auth()->id())
            ->withCount('transactions')
            ->with(['transactions' => function ($query) {
                $query->selectRaw('category_id, SUM(CASE WHEN type = "income" THEN amount ELSE -amount END) as total_amount')
                    ->groupBy('category_id');
            }]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type') && $request->get('type') !== 'all') {
            $query->where('type', $request->get('type'));
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->get('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'sort_order');
        $sortDirection = $request->get('direction', 'asc');
        
        if ($sortBy === 'transactions_count') {
            $query->orderBy('transactions_count', $sortDirection);
        } elseif ($sortBy === 'name') {
            $query->orderBy('name', $sortDirection);
        } else {
            $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
        }

        $categories = $query->paginate(15)->withQueryString();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $validated['user_id'] = auth()->id();
            
            Category::create($validated);
            
            return redirect()->route('categories.index')
                ->with('success', 'Category created successfully.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        $this->authorize('view', $category);
        
        $category->load(['transactions' => function ($query) {
            $query->with('account')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc');
        }]);
        
        // Calculate category statistics
        $stats = [
            'total_transactions' => $category->transactions->count(),
            'total_amount' => $category->transactions->sum(function ($transaction) {
                return $transaction->type === 'income' ? $transaction->amount : -$transaction->amount;
            }),
            'income_total' => $category->transactions->where('type', 'income')->sum('amount'),
            'expense_total' => $category->transactions->where('type', 'expense')->sum('amount'),
        ];
        
        return view('categories.show', compact('category', 'stats'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        $this->authorize('update', $category);
        
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(CreateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);
        
        try {
            $validated = $request->validated();
            $category->update($validated);
            
            return redirect()->route('categories.index')
                ->with('success', 'Category updated successfully.');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);
        
        try {
            // Check if category has transactions
            if ($category->transactions()->count() > 0) {
                return redirect()->route('categories.index')
                    ->with('error', 'Cannot delete category with existing transactions. Please reassign or delete the transactions first.');
            }
            
            $category->delete();
            
            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }
}