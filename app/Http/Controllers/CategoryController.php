<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(): View
    {
        // TODO: Fetch categories with transaction counts
        // $categories = Category::withCount('transactions')->get();
        
        return view('categories.index');
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // TODO: Validate and store category
        // $request->validate([
        //     'name' => 'required|string|max:255|unique:categories',
        //     'type' => 'required|in:income,expense',
        //     'color' => 'nullable|string|max:7', // Hex color code
        //     'icon' => 'nullable|string|max:50',
        // ]);
        
        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     */
    public function show(string $id): View
    {
        // TODO: Fetch category with recent transactions
        // $category = Category::with(['transactions' => function($query) {
        //     $query->orderBy('date', 'desc')->limit(10);
        // }])->findOrFail($id);
        
        return view('categories.show');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(string $id): View
    {
        // TODO: Fetch category for editing
        // $category = Category::findOrFail($id);
        
        return view('categories.edit');
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        // TODO: Validate and update category
        // $request->validate([
        //     'name' => 'required|string|max:255|unique:categories,name,' . $id,
        //     'type' => 'required|in:income,expense',
        //     'color' => 'nullable|string|max:7',
        //     'icon' => 'nullable|string|max:50',
        // ]);
        
        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        // TODO: Check if category has transactions before deleting
        // $category = Category::findOrFail($id);
        // 
        // if ($category->transactions()->count() > 0) {
        //     return redirect()->route('categories.index')
        //         ->with('error', 'Cannot delete category with existing transactions.');
        // }
        // 
        // $category->delete();
        
        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}