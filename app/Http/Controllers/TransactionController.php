<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request): View
    {
        // TODO: Fetch user's transactions with filtering
        // $query = Transaction::where('user_id', auth()->id())
        //     ->with(['account', 'category'])
        //     ->orderBy('date', 'desc');
        
        // Apply filters
        // if ($request->filled('account_id')) {
        //     $query->where('account_id', $request->account_id);
        // }
        
        // if ($request->filled('category_id')) {
        //     $query->where('category_id', $request->category_id);
        // }
        
        // if ($request->filled('type')) {
        //     $query->where('type', $request->type);
        // }
        
        // if ($request->filled('date_from')) {
        //     $query->whereDate('date', '>=', $request->date_from);
        // }
        
        // if ($request->filled('date_to')) {
        //     $query->whereDate('date', '<=', $request->date_to);
        // }
        
        // $transactions = $query->paginate(20);
        
        return view('transactions.index');
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(): View
    {
        // TODO: Fetch accounts and categories for dropdowns
        // $accounts = Account::where('user_id', auth()->id())->get();
        // $categories = Category::all();
        
        return view('transactions.create');
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // TODO: Validate and store transaction
        // $request->validate([
        //     'account_id' => 'required|exists:accounts,id',
        //     'category_id' => 'required|exists:categories,id',
        //     'type' => 'required|in:income,expense',
        //     'amount' => 'required|numeric|min:0.01',
        //     'description' => 'required|string|max:255',
        //     'date' => 'required|date',
        // ]);
        
        // Create transaction and update account balance
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified transaction.
     */
    public function show(string $id): View
    {
        // TODO: Fetch transaction with relationships
        // $transaction = Transaction::with(['account', 'category'])->findOrFail($id);
        
        return view('transactions.show');
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(string $id): View
    {
        // TODO: Fetch transaction, accounts, and categories
        // $transaction = Transaction::findOrFail($id);
        // $accounts = Account::where('user_id', auth()->id())->get();
        // $categories = Category::all();
        
        return view('transactions.edit');
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        // TODO: Validate and update transaction
        // Also update account balances if amount or account changed
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        // TODO: Delete transaction and update account balance
        // $transaction = Transaction::findOrFail($id);
        // Update account balance before deleting
        // $transaction->delete();
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }
}